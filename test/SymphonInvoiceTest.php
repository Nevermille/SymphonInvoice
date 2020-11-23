<?php

namespace Lianhua\SymphonInvoice\test;

use Exception;
use Lianhua\SymphonInvoice\ChorusAPI;
use Lianhua\SymphonInvoice\functions\DeposerFluxFacture;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class SymphonInvoiceTest extends TestCase
{
    /**
     * The API handler
     * @var ChorusAPI
     */
    private static $api;

    public static function setUpBeforeClass(): void
    {
        self::$api = new ChorusAPI(true);
    }

    /**
     * Tests the connection
     * @return void
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testConnect(): void
    {
        self::$api->connectWithOauth(getenv("OAUTH_ID"), getenv("OAUTH_SECRET"));
        self::$api->setChorusproCredentials(getenv("CHORUS_LOGIN"), getenv("CHORUS_PASSWORD"));

        $this->assertNotNull(self::$api);
        $this->assertNotEmpty(self::$api->getBearer());
    }

    /**
     * Tests the DeposerFluxFacture function
     * @return void
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @depends testConnect
     */
    public function testDeposerFluxFacture(): void
    {
        $req = new DeposerFluxFacture();
        $req->setSyntaxeFlux(DeposerFluxFacture::IN_DP_E2_UBL_INVOICE_MIN);
        $req->setFichierFlux(__DIR__ . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . "deposerFluxFacture.xml");

        $res = self::$api->request($req);

        $this->assertEqualsCanonicalizing(0, $res["codeRetour"]);
        $this->assertEquals("GCU_MSG_01_000", $res["libelle"]);
        $this->assertNotEmpty($res["numeroFluxDepot"]);
        $this->assertEquals(date("Y-m-d"), $res["dateDepot"]);
        $this->assertEquals(DeposerFluxFacture::IN_DP_E2_UBL_INVOICE_MIN, $res["syntaxeFlux"]);
    }
}
