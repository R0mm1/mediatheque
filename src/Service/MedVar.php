<?php

namespace App\Service;


use Symfony\Component\HttpKernel\KernelInterface;

class MedVar
{
    /**
     * @var string
     */
    private $filepath;

    /**
     * @var mixed[]
     */
    private $fileContent;


    public function __construct(KernelInterface $kernel)
    {
        $this->filepath = $kernel->getProjectDir() . '/var/mediatheque.json';
        $fileContent = file_get_contents($this->filepath);
        $this->fileContent = json_decode($fileContent, true);
    }

    public function getVar(string $key, $defaultValue = false)
    {
        $return = $this->fileContent[$key] ?? $defaultValue;

        if (is_string($return) && in_array($return, ['true', 'false'])) {
            $return = ($return == 'true');
        }

        return $return;
    }

    public function setVar(string $key, $content)
    {
        if (is_bool($content)) {
            $content = $content ? 'true' : 'false';
        } elseif (!is_string($content)) {
            throw new \InvalidArgumentException("The content must be a boolean or a string");
        }

        $this->fileContent[$key] = $content;

        file_put_contents($this->filepath, json_encode($this->fileContent, JSON_PRETTY_PRINT));
    }
}