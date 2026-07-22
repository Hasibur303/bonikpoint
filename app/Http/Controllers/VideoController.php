<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VideoController extends Controller
{
    private const CHANNELS = [
        [
            'name' => 'Daily Vlogs by Nayeem',
            'handle' => '@DailyvlogsbyNayeem',
            'channel_id' => 'UC3ztSs8g5Gv4Ktnjpw9CswQ',
            'url' => 'https://youtube.com/@dailyvlogsbynayeem?si=FjjidXNIp0SrnIfI',
        ],
        [
            'name' => 'Nayeem Rahman Vlogs',
            'handle' => '@nayeemrahmanvlogs',
            'channel_id' => 'UCZ1iVxgAGG8RsNif7sVXGfQ',
            'url' => 'https://youtube.com/@nayeemrahmanvlogs?si=Usa-_Z8h8J_p32JP',
        ],
        [
            'name' => 'Budget Koto',
            'handle' => '@budgetkoto8255',
            'channel_id' => 'UCRjYKIPCDVu7ev1_W5_Nx4Q',
            'url' => 'https://youtube.com/@budgetkoto?si=cO3IVqaCdoG3aNJa',
        ],
    ];

    public function index(): View
    {
        $channels = collect(self::CHANNELS)
            ->map(fn (array $channel) => [
                ...$channel,
                'videos' => $this->videosFor($channel),
            ]);

        return view('pages.videos', [
            'channels' => $channels,
            'featuredVideo' => $channels->flatMap(fn ($channel) => $channel['videos'])->first(),
        ]);
    }

    private function videosFor(array $channel): array
    {
        return Cache::remember('youtube_channel_videos_'.$channel['channel_id'], now()->addHour(), function () use ($channel) {
            $response = Http::timeout(10)->get('https://www.youtube.com/feeds/videos.xml', [
                'channel_id' => $channel['channel_id'],
            ]);

            if (! $response->successful()) {
                return [];
            }

            $xml = simplexml_load_string($response->body());

            if (! $xml) {
                return [];
            }

            return collect(iterator_to_array($xml->entry, false))
                ->take(8)
                ->map(function ($entry) use ($channel) {
                    $yt = $entry->children('http://www.youtube.com/xml/schemas/2015');
                    $media = $entry->children('http://search.yahoo.com/mrss/');
                    $videoId = (string) ($yt->videoId ?? '');

                    if ($videoId === '') {
                        $videoId = Str::after((string) $entry->id, 'yt:video:');
                    }

                    return [
                        'id' => $videoId,
                        'title' => (string) $entry->title,
                        'channel' => $channel['name'],
                        'channel_handle' => $channel['handle'],
                        'published_at' => filled((string) $entry->published) ? date('d M Y', strtotime((string) $entry->published)) : null,
                        'thumbnail' => (string) ($media->group->thumbnail->attributes()->url ?? "https://i.ytimg.com/vi/{$videoId}/hqdefault.jpg"),
                        'embed_url' => "https://www.youtube.com/embed/{$videoId}?rel=0",
                        'watch_url' => "https://www.youtube.com/watch?v={$videoId}",
                    ];
                })
                ->filter(fn (array $video) => filled($video['id']))
                ->values()
                ->all();
        });
    }
}
