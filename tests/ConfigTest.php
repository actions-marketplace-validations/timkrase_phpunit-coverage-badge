<?php

namespace PhpUnitCoverageBadge;

use Assert\Assertion;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testNoCloverFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(Assertion::INVALID_STRING);

        new Config();
    }

    /**
     * @runInSeparateProcess
     */
    public function testWrongCloverFile(): void
    {
        putenv('INPUT_CLOVER_REPORT=tests/resources/notexistingcloverfile.xml');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(Assertion::INVALID_FILE);

        new Config();
    }

    /**
     * @runInSeparateProcess
     */
    public function testNoBadgePath(): void
    {
        $this->addValidClover();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(Assertion::INVALID_STRING);

        new Config();
    }

    /**
     * @runInSeparateProcess
     */
    public function testDefaultPushBadge(): void
    {
        $this->addValidClover();
        $this->addValidBadgePath();
        $this->addValidRepoToken();
        $this->addValidCommitMessage();
        $this->addDefaultCommitEmail();
        $this->addDefaultCommitName();

        $config = new Config();

        $this->assertSame(false, $config->isPushBadge());
    }

    /**
     * @runInSeparateProcess
     */
    public function testEnabledPushMessageNoRepoToken(): void
    {
        $this->addValidClover();
        $this->addValidBadgePath();
        $this->addValidCommitMessage();
        $this->addDefaultRepoToken();
        $this->addDefaultCommitEmail();
        $this->addDefaultCommitName();

        putenv('INPUT_PUSH_BADGE=true');

        $this->expectExceptionMessage(Config::NO_REPO_TOKEN_EXCEPTION);

        new Config();
    }

    /**
     * @runInSeparateProcess
     */
    public function testNoRepoToken(): void
    {
        $this->addValidClover();
        $this->addValidBadgePath();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(Assertion::INVALID_STRING);

        new Config();
    }

    /**
     * @runInSeparateProcess
     */
    public function testNoCommitMessage(): void
    {
        $this->addValidClover();
        $this->addValidBadgePath();
        $this->addValidRepoToken();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(Assertion::INVALID_STRING);

        new Config();
    }

    /**
     * @runInSeparateProcess
     */
    public function testAllValidNoPush(): void
    {
        $this->addValidClover();
        $this->addValidBadgePath();
        $this->addValidRepoToken();
        $this->addValidCommitMessage();
        $this->addDefaultCommitEmail();
        $this->addDefaultCommitName();
        putenv('INPUT_PUSH_BADGE=true');

        $config = new Config();

        $this->assertEquals(realpath(__DIR__ . '/../tests/resources/clover_valid_29.xml'), realpath($config->getCloverFilePath()));
        $this->assertEquals(realpath(__DIR__ . '/../' . 'badge.svg'), realpath($config->getBadgePath()));
        $this->assertEquals('testtesttest', $config->getRepoToken());
        $this->assertEquals('Default Commit Message', $config->getCommitMessage());
        $this->assertEquals('41898282+github-actions[bot]@users.noreply.github.com', $config->getCommitEmail());
        $this->assertEquals('Github Actions Bot', $config->getCommitName());
        $this->assertEquals(true, $config->getPushBadge());
    }

    /**
     * @runInSeparateProcess
     */
    public function testAllValidWithPush(): void
    {
        $this->addValidClover();
        $this->addValidBadgePath();
        $this->addValidRepoToken();
        $this->addValidCommitMessage();
        $this->addDefaultCommitEmail();
        $this->addDefaultCommitName();

        $config = new Config();

        $this->assertEquals(realpath(__DIR__ . '/../tests/resources/clover_valid_29.xml'), realpath($config->getCloverFilePath()));
        $this->assertEquals(realpath(__DIR__ . '/../' . 'badge.svg'), realpath($config->getBadgePath()));
        $this->assertEquals('testtesttest', $config->getRepoToken());
        $this->assertEquals('Default Commit Message', $config->getCommitMessage());
        $this->assertEquals('41898282+github-actions[bot]@users.noreply.github.com', $config->getCommitEmail());
        $this->assertEquals('Github Actions Bot', $config->getCommitName());
        $this->assertEquals(false, $config->getPushBadge());
    }

    private function addValidClover(): void
    {
        putenv('INPUT_CLOVER_REPORT=tests/resources/clover_valid_29.xml');
    }

    private function addValidBadgePath(): void
    {
        putenv('INPUT_COVERAGE_BADGE_PATH=badge.svg');
    }

    private function addValidRepoToken(): void
    {
        putenv('INPUT_REPO_TOKEN=testtesttest');
    }

    private function addValidCommitMessage(): void
    {
        putenv('INPUT_COMMIT_MESSAGE=Default Commit Message');
    }

    private function addDefaultRepoToken(): void
    {
        putenv('INPUT_REPO_TOKEN=' . Config::REPO_TOKEN_DEFAULT);
    }

    private function addDefaultCommitEmail(): void
    {
        putenv('INPUT_COMMIT_EMAIL=41898282+github-actions[bot]@users.noreply.github.com');
    }

    private function addDefaultCommitName(): void
    {
        putenv('INPUT_COMMIT_NAME=Github Actions Bot');
    }
}