<?php

namespace App\Jobs;

use App\Models\Podcast;
use Carbon\CarbonInterval;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessPodcastUrl implements ShouldQueue
{
    use Queueable;

    public $rssUrl;
    public $listeningParty;
    public $episode;
    /**
     * Create a new job instance.
     */
    public function __construct($rssUrl, $listeningParty, $episode)
    {
        $this->rssUrl = $rssUrl;
        $this->listeningParty = $listeningParty;
        $this->episode = $episode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $xml = simplexml_load_file($this->rssUrl);

        $podcastTitle = (string) $xml->channel->title;
        $podcastAtworkUrl = $xml->channel->image->url;


        $latestEpisode = $xml->channel->item[0];
        $episodeTitle = $latestEpisode->title;
        $episadeMediaUrl = (string) $latestEpisode->enclosure['url'];

        $namespace = $xml->getNamespaces(true);
        $itunesNamespace = $namespace['itunes'];

        $episodeLength = $latestEpisode->children($itunesNamespace)->duration;

        $interval = CarbonInterval::createFromFormat('H:i:s', $episodeLength);

        $endTime = $this->listeningParty->start_time->add($interval);

        //save the podcast details to the database
        $podcast = Podcast::updateOrCreate([
            'title' => $podcastTitle,
            'artwork_url' => $podcastAtworkUrl,
            'rss_url' => $this->rssUrl,
        ]);

        //save the episode details to the database

        $this->episode->podcast()->associate($podcast);

        $this->episode->update([
            'title' => $episodeTitle,
            'media_url' => $episadeMediaUrl,
        ]);

        $this->listeningParty->update([
            'end_time' => $endTime,
        ]);

    }
}
