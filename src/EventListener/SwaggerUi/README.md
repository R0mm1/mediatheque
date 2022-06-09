When we make a request to an endpoint serving a file (like the endpoints to download the electronic and audio books)
through the browser, the "accept" header will most likely have `text/html` with a more important weight than `*/*.`
In development environment, it means the file won't be downloaded, and we will have the swagger documentation displayed
instead. This is because how the \ApiPlatform\Core\Bridge\Symfony\Bundle\EventListener\SwaggerUiListener works.

To fix that behaviour, we have the BackupControllerListener executed _before_ the SwaggerUiListener to remind which 
controller was previously set by Api Platform, end the RestoreControllerListener executed _after_ the SwaggerUiListener 
to restore the value if we are not on the api_doc route.
