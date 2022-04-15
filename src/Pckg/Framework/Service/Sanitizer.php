<?php

namespace Pckg\Framework\Service;

use HtmlSanitizer\Node\TagNodeInterface;
use HtmlSanitizer\SanitizerBuilder;
use HtmlSanitizer\Visitor\AbstractNodeVisitor;

class Sanitizer
{
    /**
     * @return string
     */
    public function sanitizeContent($untrustedHtml)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Attr.AllowedRel', ['nofollow noopener']);
        $config->set('HTML.SafeIframe', true);
        $config->set('HTML.DefaultInvalidImageAlt', null);
        $config->set('CSS.MaxImgLength', '1920px');
        $config->set('CSS.Proprietary', true);
        $config->set('Cache.SerializerPath', path('tmp'));
        // http://htmlpurifier.org/live/configdoc/plain.html#CSS.AllowedProperties
        // Filter.ExtractStyleBlocks.Scope, Filter.ExtractStyleBlocks
        // HTML.Allowed, HTML.AllowedAttributes, HTML.AllowedElements
        $config->set('HTML.Nofollow', true);
        $config->set('HTML.TargetBlank', true);
        $config->set('URI.DefaultScheme', 'https');
        // URI.Munge
        $config->set('HTML.Allowed', 'iframe[src],h1,h2,h3,h4,h5,h6,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span,img[width|height|alt|src]');
        $purifier = new \HTMLPurifier($config);

        return $purifier->purify($untrustedHtml);
    }

    /**
     * @return \Closure
     */
    private function getVueBinds()
    {
        return function (\DOMNode $domNode, TagNodeInterface $tagNode, array $attrs, array $vals) {
            foreach ($attrs as $attr) {
                /**
                 * Get value;
                 */
                $value = collect($domNode->attributes)->first(function ($a) use ($attr) {
                        return $a->name === $attr;
                })->value ?? null;

                /**
                 * Skip when value is not set.
                 */
                if (!$value) {
                    continue;
                }

                /**
                 * Skip when value is not valid.
                 */
                if (!in_array($value, $vals)) {
                    continue;
                }

                /**
                 * Set attribute;
                 */
                $tagNode->setAttribute($attr, $value);
            }
        };
    }

    /**
     * @return \Closure
     */
    public function getGeneralConfig()
    {
        /**
         * Vue whitelisting helper.
         */
        $vueBind = $this->getVueBinds();

        return function (array $config, AbstractNodeVisitor $nodeVisitor, $tagName) use ($vueBind) {

            $toMerge = [];
            $configs = [
                [
                    'tags' => ['a'],
                    'allowed_attributes' => ['href', 'title', 'alt', 'target'],
                    'processor' => function (\DOMNode $domNode, TagNodeInterface $tagNode) use ($vueBind) {
                        $vueBind($domNode, $tagNode, [':title'], ['content.title']);
                        $vueBind($domNode, $tagNode, [':href'], ['content.url', 'content.image', 'content.picture']);
                    },
                ],
                [
                    'tags' => ['img'],
                    'allowed_attributes' => ['src', 'width', 'height', 'alt', 'title'],
                    'processor' => function (\DOMNode $domNode, TagNodeInterface $tagNode) use ($vueBind) {
                        $vueBind($domNode, $tagNode, [':src'], ['content.image', 'content.picture']);
                        $vueBind($domNode, $tagNode, [':title'], ['content.title']);
                        $vueBind($domNode, $tagNode, [':alt'], ['content.title', 'content.description']);
                    },
                ],
                [
                    'tags' => ['span', 'div', 'p'],
                    'processor' => function (\DOMNode $domNode, TagNodeInterface $tagNode) use ($vueBind) {
                        $vueBind($domNode, $tagNode, ['v-html'], ['content.content']);
                    },
                ],
                [
                    'processors' => function (\DOMNode $domNode, TagNodeInterface $tagNode) use ($vueBind) {
                        return;
                        $vueBind($domNode, $tagNode, [':href'], ['content.url', 'content.image', 'content.picture']);
                    },
                ],
            ];

            foreach ($configs as $config) {
                if (isset($config['tags']) && !in_array($tagName, $config['tags'])) {
                    continue;
                }

                if (isset($config['allowed_attributes'])) {
                    $toMerge['allowed_attributes'] = array_merge($toMerge['allowed_attributes'] ?? [], $config['allowed_attributes']);
                }

                if (isset($config['processor'])) {
                    if (isset($toMerge['processor'])) {
                        $processor = $toMerge['processor'];
                        $existing = $config['processor'];
                        $toMerge['processor'] = function (...$props) use ($processor, $existing) {
                            $existing(...$props);
                            $processor(...$props);
                        };
                    } else {
                        $toMerge['processor'] = $config['processor'];
                    }
                }
            }

            return $toMerge;
        };
    }

    /**
     * @return array
     */
    private function getBuildConfig()
    {
        return [
            'extensions' => ['basic', 'code', 'image', 'list', 'table', 'iframe', 'details', 'extra'],
            'tags' => [
                'a' => [
                    'allowed_hosts' => null,
                    'allow_mailto' => true,
                    'force_https' => true,
                    'allowed_schemes' => ['https', null, 'http'],
                ],
                'img' => [
                    'allowed_hosts' => null,
                    'allow_data_uri' => false,
                    'force_https' => true,
                    'allowed_schemes' => ['https', null, 'http'],
                ],
                'iframe' => [
                    'allowed_hosts' => null,
                    'force_https' => true,
                    'allowed_schemes' => ['https'],
                ],
                'span' => [
                    'allowed_attributes' => ['itemprop', 'property', 'typeof'],
                ],
            ]];
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function sanitizeTemplate($untrustedHtml)
    {
        $builder = SanitizerBuilder::createDefault();
        $config = $this->getBuildConfig();
        $config['general'] = $this->getGeneralConfig();
        $sanitizer = $builder->build($config);

        return $sanitizer->sanitize($untrustedHtml);
    }
}
