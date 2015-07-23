<?php

namespace Vinelab\Youtube\Validators;

class ExpectedYoutubeResponse
{
    /**
     * return the expected video response.
     *
     * @return array
     */
    public static function video()
    {
        return
            $expected_video = [
                'kind' => 'example',
                'etag' => 'example',
                'pageInfo' => [
                    'totalResults' => 'example',
                    'resultsPerPage' => 'example',
                ],
                'items' => [
                    [
                    'kind' => 'example',
                    'etag' => 'example',
                    'id' => 'example',
                    'snippet' => [
                        'publishedAt' => 'example',
                        'channelId' => 'example',
                        'title' => 'example',
                        'description' => 'example',
                        'thumbnails' => [
                            'default' => [
                                'url' => 'example',
                                'width' => 'example',
                                'height' => 'example',
                            ],
                            'medium' => [
                                'url' => 'example',
                                'width' => 'example',
                                'height' => 'example',
                            ],
                            'high' => [
                                'url' => 'example',
                                'width' => 'example',
                                'height' => 'example',
                            ],
                            'standard' => [
                                'url' => 'example',
                                'width' => 'example',
                                'height' => 'example',
                            ],
                            'maxres' => [
                                'url' => 'example',
                                'width' => 'example',
                                'height' => 'example',
                            ],
                        ],
                        'channelTitle' => 'example',
                        'categoryId' => 'example',
                        'liveBroadcastContent' => 'example',
                    ],
                ],
                ],
            ];
    }

    /**
     * return the expected channel response.
     *
     * @return array
     */
    public static function channel()
    {
        return $expected_channel = [
                'kind' => 'example',
                'etag' => 'example',
                'pageInfo' => [
                    'totalResults' => 'example',
                    'resultsPerPage' => 'example',
                ],
                'items' => [[
                    'kind' => 'example',
                    'etag' => 'example',
                    'id' => 'example',
                    'snippet' => [
                        'title' => 'example',
                        'description' => 'example',
                        'publishedAt' => 'example',
                        'thumbnails' => [
                            'default' => [
                                'url' => 'example',
                            ],
                            'medium' => [
                                'url' => 'example',
                            ],
                            'high' => [
                                'url' => 'example',
                            ],
                        ],
                    ],
                    'contentDetails' => [
                        'relatedPlaylists' => [
                            'likes' => 'example',
                            'favorites' => 'example',
                            'uploads' => 'example',
                        ],
                        'googlePlusUserId' => 'example',
                    ],
                ]],
            ];
    }

    public static function search()
    {
        return $expected_search = [
            'kind' => 'example',
            'etag' => 'example',
            'id' => [
                'kind' => 'example',
                'videoId' => 'example',
            ],
            'snippet' => [
                'publishedAt' => 'example',
                'channelId' => 'example',
                'title' => 'example',
                'description' => 'example',
                'thumbnails' => [
                    'default' => [
                        'url' => 'example',
                    ],
                    'medium' => [
                        'url' => 'example',
                    ],
                    'high' => [
                        'url' => 'example',
                    ],
                ],
                'channelTitle' => 'example',
                'liveBroadcastContent' => 'example',
            ],
        ];
    }
}
