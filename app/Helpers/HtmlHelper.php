<?php

namespace App\Helpers;

class HtmlHelper
{
    /**
     * HTMLタグを壊さずに指定文字数で切り詰める
     */
    public static function limitHtml(string $html, int $limit, string $end = '…'): string
    {
        if (mb_strlen(strip_tags($html)) <= $limit) {
            return $html;
        }
        $dom = new \DOMDocument();
        @$dom->loadHTML('<meta charset="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $count     = 0;
        $truncated = false;
        $walk = function (\DOMNode $node) use (&$count, &$truncated, $limit, $end, &$walk): void {
            if ($truncated) {
                $node->parentNode?->removeChild($node);
                return;
            }
            if ($node->nodeType === XML_TEXT_NODE) {
                $remaining = $limit - $count;
                if (mb_strlen($node->nodeValue) > $remaining) {
                    $node->nodeValue = mb_substr($node->nodeValue, 0, $remaining) . $end;
                    $truncated = true;
                } else {
                    $count += mb_strlen($node->nodeValue);
                }
            } else {
                foreach (iterator_to_array($node->childNodes) as $child) {
                    $walk($child);
                }
            }
        };
        foreach (iterator_to_array($dom->childNodes) as $child) {
            $walk($child);
        }
        $result = '';
        foreach ($dom->childNodes as $child) {
            $result .= $dom->saveHTML($child);
        }
        return preg_replace('/<meta charset="UTF-8">/i', '', $result);
    }
}
