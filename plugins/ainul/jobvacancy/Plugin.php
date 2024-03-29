<?php namespace Ainul\JobVacancy;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }

    public function boot()
		{
		    \Event::listen('offline.sitesearch.query', function ($query) {

		        // Search your plugin's contents
		        $items = Models\Job::where('job_title', 'like', "%${query}%")
		                                        ->orWhere('company_name', 'like', "%${query}%")
		                                        ->get();

		        // Now build a results array
		        $results = $items->map(function ($item) use ($query) {

		            // If the query is found in the title, set a relevance of 2
		            $relevance = mb_stripos($item->title, $query) !== false ? 2 : 1;
		            
		            // Optional: Add an age penalty to older results. This makes sure that
		            // never results are listed first.
		            // if ($relevance > 1 && $item->published_at) {
		            //     $relevance -= $this->getAgePenalty($item->published_at->diffInDays(Carbon::now()));
		            // }

		            return [
		                'title'     => $item->job_title,
		                'text'      => $item->company_name,
		                'url'       => '/job/' . $item->id,
		                
		                'relevance' => $relevance, // higher relevance results in a higher
		                                           // position in the results listing
		                // 'meta' => 'data',       // optional, any other information you want
		                                           // to associate with this result
		                // 'model' => $item,       // optional, pass along the original model
		            ];
		        });

		        return [
		            'provider' => 'Job', // The badge to display for this result
		            'results'  => $results,
		        ];
		    });
		}
}
