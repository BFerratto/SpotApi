<?php
namespace SpotApi\Commands;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SpotApi\Models\Genre;

class ImportCommand extends ShopwareCommand
{
    protected static $defaultName = 'spotapi:import';
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Import data from spotify API.')
            ->addArgument(
                'client_id',
                InputArgument::REQUIRED,
                'The client Id.'
            )
            ->addArgument(
                'client_secret',
                InputArgument::REQUIRED,
                'The client secret.'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> imports data from spotify API.
EOF
            )
        ;
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientId = $input->getArgument('client_id');
        $clientSecret = $input->getArgument('client_secret');
        $output->writeln('<info>Trying to get spotify token</info>');
        $tokenData = $spotifyToken = $this->getSpotifyToken($clientId, $clientSecret);
        echo "GOT SPOTIFY TOKEN {$spotifyToken}";
        $output->writeln('<info>Trying to get genres</info>');
        $this->getGenres($tokenData["access_token"]);
    }
    private function getSpotifyToken($clientId, $clientSecret) {
        $authString = base64_encode("{$clientId}:${clientSecret}");
        $options = array(
            'http' => array(
                'header'  =>
                    "Authorization: Basic {$authString}"
            ,
                'method'  => 'POST',
                'content' => http_build_query(['grant_type'=>'client_credentials'])
            )
        );
        $url = "https://accounts.spotify.com/api/token";
        $ctx  = stream_context_create($options);
        $result = file_get_contents($url, false, $ctx);
        if ($result === FALSE) {
            throw new Exception('Spotify token not retrievable');
        }
        return json_decode($result, true);
    }
    private function makeRequest($endPoint, $token) {
        $url ="https://api.spotify.com/v1/{$endPoint}";
        $options = array(
            'http' => array(
                'header'  =>
                    "Content-type: application/json\r\n".
                    "Accept-type: application/json\r\n".
                    "Authorization: Bearer {$token}"
            ,
                'method'  => 'GET',
            )
        );
        $ctx  = stream_context_create($options);
        $result = file_get_contents($url, false, $ctx);
        $response = json_decode($result,true);
        return $response["genres"];
    }
    protected function getGenres($token) {
        $genres = $this->makeRequest("recommendations/available-genre-seeds",$token);

        /** @var \Shopware\Components\Model\ModelManager $entityManager */
        $em = $this->container->get('models');
        $batchSize = 100;
        $insertCounter = 0;
        foreach($genres as $title) {
            $insertCounter++;
            $genreModel = new Genre();
            $genreModel->title = $title;
            $em->persist($genreModel);
            if (($insertCounter % $batchSize) === 0) {
                $em->flush();
                $em->clear();
            }
        }
        $em->flush();
        $em->clear();
    }

}