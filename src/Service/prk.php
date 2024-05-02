<?php
namespace App\Service;

class prk
{
    private $badWords = ['bonjour','ahmed','ali']; // Ajoute ici ta liste de mots interdits

    public function filterText(string $text): string
    {
        // Remplace tous les mots interdits par des étoiles
        foreach ($this->badWords as $badWord) {
            $text = str_ireplace($badWord, str_repeat('*', strlen($badWord)), $text);
        }

        return $text;
    }
}
