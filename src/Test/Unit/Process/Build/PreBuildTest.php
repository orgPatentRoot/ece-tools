<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MagentoCloud\Test\Unit\Process\Build;

use Magento\MagentoCloud\Config\Build;
use Magento\MagentoCloud\Config\Environment;
use Magento\MagentoCloud\Process\Build\PreBuild;
use Magento\MagentoCloud\Package\Manager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class PreBuildTest extends TestCase
{
    /**
     * @var PreBuild
     */
    private $process;

    /**
     * @var Build|\PHPUnit_Framework_MockObject_MockObject
     */
    private $buildConfigMock;

    /**
     * @var Environment|\PHPUnit_Framework_MockObject_MockObject
     */
    private $environmentMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * @var Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $packageManagerMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->buildConfigMock = $this->getMockBuilder(Build::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->environmentMock = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->getMockForAbstractClass();
        $this->packageManagerMock = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->process = new PreBuild(
            $this->buildConfigMock,
            $this->environmentMock,
            $this->loggerMock,
            $this->packageManagerMock
        );
    }

    /**
     * @param string $verbosity
     * @param string $expectedVerbosity
     * @dataProvider executeDataProvider
     */
    public function testExecute(string $verbosity, string $expectedVerbosity)
    {
        $this->buildConfigMock->expects($this->once())
            ->method('getVerbosityLevel')
            ->willReturn($verbosity);
        $this->loggerMock->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                ['Verbosity level is ' . $expectedVerbosity],
                ['Starting build. Some info.']
            );
        $this->environmentMock->expects($this->once())
            ->method('removeFlagStaticContentInBuild');
        $this->packageManagerMock->expects($this->once())
            ->method('getPrettyInfo')
            ->willReturn('Some info.');

        $this->process->execute();
    }

    /**
     * @return array
     */
    public function executeDataProvider(): array
    {
        return [
            'verbosity very' => [' -vvv', ' -vvv'],
            'verbosity none' => ['', 'not set'],
        ];
    }
}