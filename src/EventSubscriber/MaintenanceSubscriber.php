<?php
namespace App\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    private $display;

    public function __construct($display)
    {
        $this->display = $display;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // Si le param display est à off
        if(!$this->display) {
            return;
        }
        // On récupère la réponse contenue dans l'envent FilterResponseEvent
        $response = $event->getResponse();
        // Code HTML de notre bannière
        $bannerHtml = '<div class="container mt-3"><div class="alert alert-danger" role="alert">
          Maintenance prévue le  entre 23h et 5h.
        </div></div>';
        // On récupère le contenu HTML de la réponse
        $content = $response->getContent();
        // On "injecte" notre bannière au niveau de <body>
        // On remplace body par body + notre bannière
        $content = str_replace(
            '<main class="col-md-10 ml-auto mr-auto mt-3 pt-5 mb-5">',
            '<main class="col-md-10 ml-auto mr-auto mt-3 pt-5 mb-5">'.$bannerHtml,
            $content
        );
        $response->setContent($content);
    }
    
    public static function getSubscribedEvents()
    {
        return [
           'kernel.response' => 'onKernelResponse',
        ];
    }
}