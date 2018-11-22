<?php
/**
 * Created by PhpStorm.
 * User: romain2
 * Date: 29/10/18
 * Time: 13:45
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiGeneralController extends AbstractController
{
    /**
     * @Route("/api/general/uploadTempFile", name="api_general_uploadTempFile", methods="POST")
     */
    public function uploadTempFileAction(Request $request)
    {
        $params = $this->getParameters($request);
        $data = explode(',', $params['src']);

        $fileName = sha1($data[1]);
        if (!empty($params['mime'])) {
            $extensionGuesser = new MimeTypeExtensionGuesser();
            $fileName .= $extensionGuesser->guess($params['mime']);
        }

        $file = fopen($this->get('kernel')->getProjectDir() . '/public/' . $fileName, 'wb');

        fwrite($file, base64_decode($data[1]));

        fclose($file);

        return $this->json([
            'src'=>
        ]);
    }
}