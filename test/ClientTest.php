<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap;

require_once __DIR__ . '/TestAsset/commontypes.php';

use Laminas\Soap\Client;
use Laminas\Soap\Server;

/**
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage UnitTests
 * @group      Laminas_Soap
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }
    }

    public function testSetOptions()
    {
        /*************************************************************
         * ------ Test WSDL mode options -----------------------------
         *************************************************************/
        $client = new Client();

        $this->assertTrue($client->getOptions() == array('encoding' => 'UTF-8', 'soap_version' => SOAP_1_2));

        $ctx = stream_context_create();

        $nonWSDLOptions = array('soap_version'   => SOAP_1_1,
                                'classmap'       => array('TestData1' => '\LaminasTest\Soap\TestAsset\TestData1',
                                                    'TestData2' => '\LaminasTest\Soap\TestAsset\TestData2',),
                                'encoding'       => 'ISO-8859-1',
                                'uri'            => 'https://getlaminas.org/Laminas_Soap_ServerTest.php',
                                'location'       => 'https://getlaminas.org/Laminas_Soap_ServerTest.php',
                                'use'            => SOAP_ENCODED,
                                'style'          => SOAP_RPC,

                                'login'          => 'http_login',
                                'password'       => 'http_password',

                                'proxy_host'     => 'proxy.somehost.com',
                                'proxy_port'     => 8080,
                                'proxy_login'    => 'proxy_login',
                                'proxy_password' => 'proxy_password',

                                'local_cert'     => __DIR__.'/TestAsset/cert_file',
                                'passphrase'     => 'some pass phrase',

                                'stream_context' => $ctx,
                                'cache_wsdl'     => 8,
                                'features'       => 4,

                                'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5);

        $client->setOptions($nonWSDLOptions);
        $this->assertTrue($client->getOptions() == $nonWSDLOptions);


        /*************************************************************
         * ------ Test non-WSDL mode options -----------------------------
         *************************************************************/
        $client1 = new Client();

        $this->assertTrue($client1->getOptions() == array('encoding' => 'UTF-8', 'soap_version' => SOAP_1_2));

        $wsdlOptions = array('soap_version'   => SOAP_1_1,
                             'wsdl'           => __DIR__.'/TestAsset/wsdl_example.wsdl',
                             'classmap'       => array('TestData1' => '\LaminasTest\Soap\TestAsset\TestData1',
                                                 'TestData2' => '\LaminasTest\Soap\TestAsset\TestData2',),
                             'encoding'       => 'ISO-8859-1',

                             'login'          => 'http_login',
                             'password'       => 'http_password',

                             'proxy_host'     => 'proxy.somehost.com',
                             'proxy_port'     => 8080,
                             'proxy_login'    => 'proxy_login',
                             'proxy_password' => 'proxy_password',

                             'local_cert'     => __DIR__.'/TestAsset/cert_file',
                             'passphrase'     => 'some pass phrase',

                             'stream_context' => $ctx,

                             'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5);

        $client1->setOptions($wsdlOptions);
        $this->assertTrue($client1->getOptions() == $wsdlOptions);
    }

    public function testGetOptions()
    {
        $client = new Client();

        $this->assertTrue($client->getOptions() == array('encoding' => 'UTF-8', 'soap_version' => SOAP_1_2));

        $options = array('soap_version'   => SOAP_1_1,
                         'wsdl'           => __DIR__.'/TestAsset/wsdl_example.wsdl',

                         'classmap'       => array('TestData1' => '\LaminasTest\Soap\TestAsset\TestData1',
                                             'TestData2' => '\LaminasTest\Soap\TestAsset\TestData2',),
                         'encoding'       => 'ISO-8859-1',
                         'uri'            => 'https://getlaminas.org/Laminas_Soap_ServerTest.php',
                         'location'       => 'https://getlaminas.org/Laminas_Soap_ServerTest.php',
                         'use'            => SOAP_ENCODED,
                         'style'          => SOAP_RPC,

                         'login'          => 'http_login',
                         'password'       => 'http_password',

                         'proxy_host'     => 'proxy.somehost.com',
                         'proxy_port'     => 8080,
                         'proxy_login'    => 'proxy_login',
                         'proxy_password' => 'proxy_password',

                         'local_cert'     => __DIR__.'/TestAsset/cert_file',
                         'passphrase'     => 'some pass phrase',

                         'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5);

        $client->setOptions($options);
        $this->assertTrue($client->getOptions() == $options);
    }

    /**
     * @group Laminas-8053
     */
    public function testGetAndSetUserAgentOption()
    {
        $client = new Client();
        $this->assertNull($client->getUserAgent());

        $client->setUserAgent('agent1');
        $this->assertEquals('agent1', $client->getUserAgent());

        $client->setOptions(array(
            'user_agent' => 'agent2'
        ));
        $this->assertEquals('agent2', $client->getUserAgent());

        $client->setOptions(array(
            'useragent' => 'agent3'
        ));
        $this->assertEquals('agent3', $client->getUserAgent());

        $client->setOptions(array(
            'userAgent' => 'agent4'
        ));
        $this->assertEquals('agent4', $client->getUserAgent());

        $options = $client->getOptions();
        $this->assertEquals('agent4', $options['user_agent']);
    }

    /**
     * @group Laminas-6954
     */
    public function testUserAgentAllowsEmptyString()
    {
        $client = new Client();
        $this->assertNull($client->getUserAgent());
        $options = $client->getOptions();
        $this->assertArrayNotHasKey('user_agent', $options);

        $client->setUserAgent('');
        $this->assertEquals('', $client->getUserAgent());
        $options = $client->getOptions();
        $this->assertEquals('', $options['user_agent']);

        $client->setUserAgent(null);
        $this->assertNull($client->getUserAgent());
        $options = $client->getOptions();
        $this->assertArrayNotHasKey('user_agent', $options);
    }

    /**
     * @group Laminas-10542
     */
    public function testAllowNumericZeroAsValueForCacheWsdlOption()
    {
        $client = new Client();
        $this->assertNull($client->getWsdlCache());
        $options = $client->getOptions();
        $this->assertArrayNotHasKey('cache_wsdl', $options);

        $client->setWsdlCache(WSDL_CACHE_NONE);
        $this->assertSame(WSDL_CACHE_NONE, $client->getWsdlCache());
        $options = $client->getOptions();
        $this->assertSame(WSDL_CACHE_NONE, $options['cache_wsdl']);

        $client->setWsdlCache(null);
        $this->assertNull($client->getWsdlCache());
        $options = $client->getOptions();
        $this->assertArrayNotHasKey('cache_wsdl', $options);
    }

    /**
     * @group Laminas-10542
     */
    public function testAllowNumericZeroAsValueForCompressionOptions()
    {
        $client = new Client();
        $this->assertNull($client->getCompressionOptions());
        $options = $client->getOptions();
        $this->assertArrayNotHasKey('compression', $options);

        $client->setCompressionOptions(SOAP_COMPRESSION_GZIP);
        $this->assertSame(SOAP_COMPRESSION_GZIP, $client->getCompressionOptions());
        $options = $client->getOptions();
        $this->assertSame(SOAP_COMPRESSION_GZIP, $options['compression']);

        $client->setCompressionOptions(null);
        $this->assertNull($client->getCompressionOptions());
        $options = $client->getOptions();
        $this->assertArrayNotHasKey('compression', $options);
    }


    public function testGetFunctions()
    {
        $server = new Server(__DIR__ . '/TestAsset/wsdl_example.wsdl');
        $server->setClass('\LaminasTest\Soap\TestAsset\TestClass');

        $client = new Client\Local($server, __DIR__ . '/TestAsset/wsdl_example.wsdl');

        $this->assertTrue($client->getFunctions() == array('string testFunc()',
                                                           'string testFunc2(string $who)',
                                                           'string testFunc3(string $who, int $when)',
                                                           'string testFunc4()'));
    }

    /**
     * @todo Implement testGetTypes().
     */
    public function testGetTypes()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    public function testGetLastRequest()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testGetLastRequest() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server(__DIR__ . '/TestAsset/wsdl_example.wsdl');
        $server->setClass('\LaminasTest\Soap\TestAsset\TestClass');

        $client = new Client\Local($server, __DIR__ . '/TestAsset/wsdl_example.wsdl');

        // Perform request
        $client->testFunc2('World');

        $expectedRequest = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                         . '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" '
                         .               'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                         .               'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                         .               'xmlns:enc="http://www.w3.org/2003/05/soap-encoding">'
                         .     '<env:Body>'
                         .         '<env:testFunc2 env:encodingStyle="http://www.w3.org/2003/05/soap-encoding">'
                         .             '<who xsi:type="xsd:string">World</who>'
                         .         '</env:testFunc2>'
                         .     '</env:Body>'
                         . '</env:Envelope>' . "\n";

        $this->assertEquals($client->getLastRequest(), $expectedRequest);
    }

    public function testGetLastResponse()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testGetLastResponse() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server(__DIR__ . '/TestAsset/wsdl_example.wsdl');
        $server->setClass('\LaminasTest\Soap\TestAsset\TestClass');

        $client = new Client\Local($server, __DIR__ . '/TestAsset/wsdl_example.wsdl');

        // Perform request
        $client->testFunc2('World');

        $expectedResponse = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                          . '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" '
                          .               'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                          .               'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                          .               'xmlns:enc="http://www.w3.org/2003/05/soap-encoding">'
                          .     '<env:Body xmlns:rpc="http://www.w3.org/2003/05/soap-rpc">'
                          .         '<env:testFunc2Response env:encodingStyle="http://www.w3.org/2003/05/soap-encoding">'
                          .             '<rpc:result>testFunc2Return</rpc:result>'
                          .             '<testFunc2Return xsi:type="xsd:string">Hello World!</testFunc2Return>'
                          .         '</env:testFunc2Response>'
                          .     '</env:Body>'
                          . '</env:Envelope>' . "\n";

        $this->assertEquals($client->getLastResponse(), $expectedResponse);
    }

    public function testCallInvoke()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testCallInvoke() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server(__DIR__ . '/TestAsset/wsdl_example.wsdl');
        $server->setClass('\LaminasTest\Soap\TestAsset\TestClass');

        $client = new Client\Local($server, __DIR__ . '/TestAsset/wsdl_example.wsdl');

        $this->assertEquals($client->testFunc2('World'), 'Hello World!');
    }

    public function testSetOptionsWithLaminasConfig()
    {
        $ctx = stream_context_create();

        $nonWSDLOptions = array('soap_version'   => SOAP_1_1,
                                'classmap'       => array('TestData1' => '\LaminasTest\Soap\TestAsset\TestData1',
                                                    'TestData2' => '\LaminasTest\Soap\TestAsset\TestData2',),
                                'encoding'       => 'ISO-8859-1',
                                'uri'            => 'https://getlaminas.org/Laminas_Soap_ServerTest.php',
                                'location'       => 'https://getlaminas.org/Laminas_Soap_ServerTest.php',
                                'use'            => SOAP_ENCODED,
                                'style'          => SOAP_RPC,

                                'login'          => 'http_login',
                                'password'       => 'http_password',

                                'proxy_host'     => 'proxy.somehost.com',
                                'proxy_port'     => 8080,
                                'proxy_login'    => 'proxy_login',
                                'proxy_password' => 'proxy_password',

                                'local_cert'     => __DIR__.'/TestAsset/cert_file',
                                'passphrase'     => 'some pass phrase',

                                'stream_context' => $ctx,

                                'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5
        );

        $config = new \Laminas\Config\Config($nonWSDLOptions);

        $client = new Client(null, $config);

        $this->assertEquals($nonWSDLOptions, $client->getOptions());
    }

    public function testSetInputHeaders()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testSetInputHeaders() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server(__DIR__ . '/TestAsset/wsdl_example.wsdl');
        $server->setClass('\LaminasTest\Soap\TestAsset\TestClass');

        $client = new Client\Local($server, __DIR__ . '/TestAsset/wsdl_example.wsdl');

        // Add request header
        $client->addSoapInputHeader(new \SoapHeader('http://www.example.com/namespace', 'MyHeader1', 'SOAP header content 1'));
        // Add permanent request header
        $client->addSoapInputHeader(new \SoapHeader('http://www.example.com/namespace', 'MyHeader2', 'SOAP header content 2'), true);

        // Perform request
        $client->testFunc2('World');

        $expectedRequest = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                         . '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" '
                         .               'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                         .               'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                         .               'xmlns:ns1="http://www.example.com/namespace" '
                         .               'xmlns:enc="http://www.w3.org/2003/05/soap-encoding">'
                         .     '<env:Header>'
                         .         '<ns1:MyHeader2>SOAP header content 2</ns1:MyHeader2>'
                         .         '<ns1:MyHeader1>SOAP header content 1</ns1:MyHeader1>'
                         .     '</env:Header>'
                         .     '<env:Body>'
                         .         '<env:testFunc2 env:encodingStyle="http://www.w3.org/2003/05/soap-encoding">'
                         .             '<who xsi:type="xsd:string">World</who>'
                         .         '</env:testFunc2>'
                         .     '</env:Body>'
                         . '</env:Envelope>' . "\n";

        $this->assertEquals($client->getLastRequest(), $expectedRequest);


        // Add request header
        $client->addSoapInputHeader(new \SoapHeader('http://www.example.com/namespace', 'MyHeader3', 'SOAP header content 3'));

        // Perform request
        $client->testFunc2('World');

        $expectedRequest = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                         . '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" '
                         .               'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                         .               'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                         .               'xmlns:ns1="http://www.example.com/namespace" '
                         .               'xmlns:enc="http://www.w3.org/2003/05/soap-encoding">'
                         .     '<env:Header>'
                         .         '<ns1:MyHeader2>SOAP header content 2</ns1:MyHeader2>'
                         .         '<ns1:MyHeader3>SOAP header content 3</ns1:MyHeader3>'
                         .     '</env:Header>'
                         .     '<env:Body>'
                         .         '<env:testFunc2 env:encodingStyle="http://www.w3.org/2003/05/soap-encoding">'
                         .             '<who xsi:type="xsd:string">World</who>'
                         .         '</env:testFunc2>'
                         .     '</env:Body>'
                         . '</env:Envelope>' . "\n";

        $this->assertEquals($client->getLastRequest(), $expectedRequest);


        $client->resetSoapInputHeaders();

        // Add request header
        $client->addSoapInputHeader(new \SoapHeader('http://www.example.com/namespace', 'MyHeader4', 'SOAP header content 4'));

        // Perform request
        $client->testFunc2('World');

        $expectedRequest = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                         . '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" '
                         .               'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                         .               'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                         .               'xmlns:ns1="http://www.example.com/namespace" '
                         .               'xmlns:enc="http://www.w3.org/2003/05/soap-encoding">'
                         .     '<env:Header>'
                         .         '<ns1:MyHeader4>SOAP header content 4</ns1:MyHeader4>'
                         .     '</env:Header>'
                         .     '<env:Body>'
                         .         '<env:testFunc2 env:encodingStyle="http://www.w3.org/2003/05/soap-encoding">'
                         .             '<who xsi:type="xsd:string">World</who>'
                         .         '</env:testFunc2>'
                         .     '</env:Body>'
                         . '</env:Envelope>' . "\n";

        $this->assertEquals($client->getLastRequest(), $expectedRequest);
    }

    /**
     * @group Laminas-6955
     */
    public function testSetCookieIsDelegatedToSoapClient()
    {
        $fixtureCookieKey = "foo";
        $fixtureCookieValue = "bar";

        $clientMock = $this->getMock('SoapClient', array('__setCookie'), array(null, array('uri' => 'https://www.zend.com', 'location' => 'https://www.zend.com')));
        $clientMock->expects($this->once())
                   ->method('__setCookie')
                   ->with($fixtureCookieKey, $fixtureCookieValue);

        $soap = new Client();
        $soap->setSoapClient($clientMock);

        $soap->setCookie($fixtureCookieKey, $fixtureCookieValue);
    }

    public function testSetSoapClient()
    {
        $clientMock = $this->getMock('SoapClient', array('__setCookie'), array(null, array('uri' => 'https://www.zend.com', 'location' => 'https://www.zend.com')));

        $soap = new Client();
        $soap->setSoapClient($clientMock);

        $this->assertSame($clientMock, $soap->getSoapClient());
    }
}
