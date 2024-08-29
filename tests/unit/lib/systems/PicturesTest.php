<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use meteocontrol\client\vcomapi\model\PictureFile;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class PicturesTest extends TestCase {

    public function testGetPicture() {
        $json = file_get_contents(__DIR__ . '/responses/getSinglePicture.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/picture'))
            ->willReturn($json);
        /** @var PictureFile $picture */
        $picture = $this->api->system('ABCDE')->picture()->get();

        $this->assertEquals(12345, $picture->id);
        $this->assertEquals('mcLogo.png', $picture->filename);
        $this->assertEquals('image/png', $picture->type);
        $this->assertEquals($this->getEncodedTestPicture(), $picture->content);
    }

    /**
     * @return string
     */
    private function getEncodedTestPicture() {
        return 'data:image/png;base64,' . base64_encode(file_get_contents(__DIR__ . '/responses/mcLogo.png'));
    }
}
