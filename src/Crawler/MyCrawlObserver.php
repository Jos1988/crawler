<?php

namespace App\Crawler;

use App\Entity\CrawlLink;
use App\Entity\Website;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;
use Spatie\Crawler\CrawlObserver;

class MyCrawlObserver extends CrawlObserver
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var int
     */
    protected $crawlCount = 0;

    /**
     * @var CrawlLink[]
     */
    protected $batch = [];

    /**
     * @var int
     */
    protected $batchSize = 50;

    /**
     * @var Website
     */
    protected $website;

    /**
     * MyCrawlObserver constructor.
     *
     * @param LoggerInterface        $logger
     * @param EntityManagerInterface $em
     * @param Website                $website
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $em, Website $website)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->website = $website;
    }

    /**
     * Called when the crawler will crawl the url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     */
    public function willCrawl(UriInterface $url)
    {
        $this->logger->info("Crawling {$url}.");
    }

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param \Psr\Http\Message\UriInterface      $url
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    ): void {
        $crawlLink = new CrawlLink();
        $crawlLink->setLink($url->getPath());

        $this->addToBatch($crawlLink);
        $this->logger->info("Added {$url->getPath()} to batch.");
        if (count($this->getBatch()) >= $this->batchSize) {
            $this->flushBatch($this->batch);
            $this->clearBatch();
        }
    }

    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param \Psr\Http\Message\UriInterface         $url
     * @param \GuzzleHttp\Exception\RequestException $requestException
     * @param \Psr\Http\Message\UriInterface|null    $foundOnUrl
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ) {
        $link = $url->getHost().$url->getPath();
        $message = $requestException::getResponseBodySummary($requestException->getResponse());

        $this->logger->error("Crawling {$link} failed: {$message}");
    }

    /**
     * get CrawlCount
     *
     * @return int
     */
    public function getCrawlCount(): int
    {
        return $this->crawlCount;
    }

    /**
     * get BatchSize
     *
     * @return int
     */
    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    /**
     * set BatchSize
     *
     * @param int $batchSize
     *
     * @return $this
     */
    public function setBatchSize(int $batchSize): self
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    /**
     * get Website
     *
     * @return Website
     */
    public function getWebsite(): Website
    {
        return $this->website;
    }

    /**
     * set Website
     *
     * @param Website $website
     *
     * @return $this
     */
    public function setWebsite(Website $website): self
    {
        $this->website = $website;

        return $this;
    }

    /**
     * get Batch
     *
     * @return CrawlLink[]
     */
    public function getBatch(): array
    {
        return $this->batch;
    }

    /**
     * set Batch
     *
     * @param CrawlLink[] $batch
     *
     * @return $this
     */
    public function setBatch(array $batch): self
    {
        $this->batch = $batch;

        return $this;
    }

    /**
     * clear batch
     *
     * @return $this
     */
    public function clearBatch(): self
    {
        $this->batch = [];

        return $this;
    }

    /**
     * Add Crawllink to batch.
     *
     * @param CrawlLink $crawlLink
     */
    public function addToBatch(CrawlLink $crawlLink): void
    {
        $this->batch[] = $crawlLink;
    }

    /**
     * Push batch items to database.
     *
     * @param CrawlLink[]|array $batch
     *
     * @return void
     */
    public function flushBatch(array $batch): void
    {
        $batchSize = count($batch);
        $this->logger->info("Persisting batch of {$batchSize} CrawlLinks.");

        $existingCrawlLinks = $this->website->getCrawlLinks();
        $setUrls = [];
        foreach ($existingCrawlLinks as $crawlLink) {
            $setUrls[] = $crawlLink->getLink();
        }

        foreach ($batch as $crawlLink) {
            if (false === in_array($crawlLink->getLink(), $setUrls)) {
                $setUrls[] = $crawlLink->getLink();
                $this->crawlCount++;

                $this->website->addCrawlLink($crawlLink);
                $crawlLink->setWebsite($this->website);

                $this->em->persist($crawlLink);
            } else {
                $batchSize--;
                $this->logger->alert("{$crawlLink->getLink()} is already in database, not persisting crawlLink.");
            }
        }

        $this->em->flush();
        $this->logger->info("Finished Persisting batch, added {$batchSize} new crawlLinks.");
    }
}