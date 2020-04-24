<?php

namespace App\Console\Commands;

use Cards;
use Illuminate\Console\Command;

class RegenerateCardImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cards:regenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates an image for each available card.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Generating card images!");

        foreach (Cards::getAll() as $card)
        {
            $relativePath = "images/generated-cards/".$card->name.".jpg";
            $absolutePath = storage_path("app/public/".$relativePath);

            $image = Cards::generateCardImage($card->id);
            $image->save($absolutePath);

            $card->image_url = "storage/".$relativePath;
            $card->save();

            $this->output->write(".");
        }
    }
}
