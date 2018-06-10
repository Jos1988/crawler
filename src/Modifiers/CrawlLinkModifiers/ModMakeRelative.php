<?php

namespace App\Modifiers\CrawlLinkModifiers;

use App\Entity\CrawlLink;
use App\Modifiers\AbstractModifier;
use App\Modifiers\ModifierInterface;

/**
 * Class Relativizer
 *
 * Makes the link relative, if host is the same as the website.
 *
 * @package App\Modifiers\CrawlLinkModifiers
 */
class ModMakeRelative implements ModifierInterface
{
    /**
     * @param CrawlLink $input
     *
     * @return mixed
     */
    public function modify($input)
    {
        $url = $input->getLink();
        $parsedUrl = parse_url($url);
        $trimmedLink = $url;

        if (isset($parsedUrl['host']) && false !== strpos($input->getWebsite()->getUrl(), $parsedUrl['host'])) {
            $trimmedLink = str_replace($input->getWebsite()->getUrl(), '', $url);
        }
        $input->setLink($trimmedLink);

        return $input;
    }

    /**
     * @inheritdoc
     */
    public function validateInput($input): bool
    {
        return is_a($input, CrawlLink::class);
    }

}