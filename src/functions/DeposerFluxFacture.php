<?php

namespace Lianhua\SymphonInvoice\functions;

/*
SymphonInvoice Library
Copyright (C) 2020  Lianhua Studio

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

use Exception;
use Lianhua\SymphonInvoice\base\ChorusAPIPath;

class DeposerFluxFacture extends ChorusAPIPath
{
    public const IN_DP_E1_UBL_INVOICE = "IN_DP_E1_UBL_INVOICE";
    public const IN_DP_E1_CII_16B = "IN_DP_E1_CII_16B";
    public const IN_DP_E1_PES_FACTURE = "IN_DP_E1_PES_FACTURE";
    public const IN_DP_E1_XCBL = "IN_DP_E1_XCBL";
    public const IN_DP_E2_UBL_INVOICE_MIN = "IN_DP_E2_UBL_INVOICE_MIN";
    public const IN_DP_E2_CII_MIN_16B = "IN_DP_E2_CII_MIN_16B";
    public const IN_DP_E2_CII_FACTURX = "IN_DP_E2_CII_FACTURX";
    public const IN_DP_E2_PES_FACTURE_MIN = "IN_DP_E2_PES_FACTURE_MIN";
    public const IN_DP_E2_CPP_FACTURE_MIN = "IN_DP_E2_CPP_FACTURE_MIN";

    /**
     * Technical id of the current user in the Chorus Pro system
     * @var int
     */
    private $idUtilisateurCourant;

    /**
     * File corresponding to the invoicing flow
     * @var string
     */
    private $fichierFlux;

    /**
     * File name with the extension
     * @var string
     */
    private $nomFichier;

    /**
     * Choice of the syntax of the file to submit
     * @var string
     */
    private $syntaxeFlux;

    /**
     * Precise if the file has been signed or not
     * @var string
     */
    private $avecSignature;

    protected function getPath(): string
    {
        return "/deposer/flux";
    }

    protected function getPostFields(): array
    {
        $res = [];

        if ($this->idUtilisateurCourant !== null) {
            $res["idUtilisateurCourant"] = $this->idUtilisateurCourant;
        }

        if ($this->fichierFlux !== null) {
            $res["fichierFlux"] = base64_encode(file_get_contents($this->fichierFlux));
        } else {
            $this->missingValueException("fichierFlux");
        }

        if ($this->nomFichier !== null) {
            $res["nomFichier"] = $this->nomFichier;
        } else {
            $this->missingValueException("nomFichier");
        }

        if ($this->syntaxeFlux !== null) {
            $res["syntaxeFlux"] = $this->syntaxeFlux;
        } else {
            $this->missingValueException("syntaxeFlux");
        }

        if ($this->avecSignature !== null) {
            $res["idUtilisateurCourant"] = $this->avecSignature;
        }

        return $res;
    }

    /**
     * Sets technical id of the current user in the Chorus Pro system
     * @param int|null $idUtilisateurCourant Technical id of the current user in the Chorus Pro system
     * @return void
     */
    public function setIdUtilisateurCourant(?int $idUtilisateurCourant): void
    {
        $this->idUtilisateurCourant = $idUtilisateurCourant;
    }

    /**
     * Gets technical id of the current user in the Chorus Pro system
     * @return int|null Technical id of the current user in the Chorus Pro system
     */
    public function getIdUtilisateurCourant(): ?int
    {
        return $this->idUtilisateurCourant;
    }

    /**
     * Sets file corresponding to the invoicing flow
     * @param string|null $fichierFlux File corresponding to the invoicing flow
     * @return void
     * @throws Exception
     */
    public function setFichierFlux(?string $fichierFlux): void
    {
        if (file_exists($fichierFlux)) {
            if (is_dir($fichierFlux)) {
                // Directories aren't allowed
                throw new Exception($fichierFlux . " is a directory");
            } else {
                $this->fichierFlux = $fichierFlux;

                /* If nomFichier is empty, we automatically set it with the basename
                in order to make things faster */
                if (empty($this->nomFichier)) {
                    $this->setNomFichier(basename($fichierFlux));
                }
            }
        } else {
            // File must exists
            throw new Exception("File not found : " . $fichierFlux);
        }
    }

    /**
     * Gets file corresponding to the invoicing flow
     * @return string|null File corresponding to the invoicing flow
     */
    public function getFichierFlux(): ?string
    {
        return $this->fichierFlux;
    }

    /**
     * Sets file name with the extension
     * @param string|null $nomFichier File name with the extension
     * @return void
     */
    public function setNomFichier(?string $nomFichier): void
    {
        $this->nomFichier = substr($nomFichier, -200);
    }

    /**
     * Gets file name with the extension
     * @return string|null File name with the extension
     */
    public function getNomFichier(): ?string
    {
        return $this->nomFichier;
    }

    /**
     * Sets choice of the syntax of the file to submit
     * @param string|null $syntaxeFlux Choice of the syntax of the file to submit
     * @return void
     */
    public function setSyntaxeFlux(?string $syntaxeFlux): void
    {
        $this->syntaxeFlux = $syntaxeFlux;
    }

    /**
     * Gets choice of the syntax of the file to submit
     * @return string|null Choice of the syntax of the file to submit
     */
    public function getSyntaxeFlux(): ?string
    {
        return $this->syntaxeFlux;
    }

    /**
     * Sets precise if the file has been signed or not
     * @param string|null $avecSignature Precise if the file has been signed or not
     * @return void
     */
    public function setAvecSignature(?string $avecSignature): void
    {
        $this->avecSignature = $avecSignature;
    }

    /**
     * Gets precise if the file has been signed or not
     * @return string|null Precise if the file has been signed or not
     */
    public function getAvecSignature(): ?string
    {
        return $this->avecSignature;
    }
}
