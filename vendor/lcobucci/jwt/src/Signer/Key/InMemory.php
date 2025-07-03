<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer\Key;

use Lcobucci\JWT\Signer\InvalidKeyProvided;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\SodiumBase64Polyfill;
use SplFileObject;
use Throwable;

use function assert;
use function is_string;

final class InMemory implements Key
{
    private string $contents;
    private string $passphrase;

    /** @param non-empty-string $contents */
    private function __construct(string $contents, string $passphrase)
    {
        // @phpstan-ignore-next-line
        if ($contents === '') {
            throw InvalidKeyProvided::cannotBeEmpty();
        }

        $this->contents   = $contents;
        $this->passphrase = $passphrase;
    }

    /** @deprecated Deprecated since v4.3 */
    public static function empty(): self
    {
        $emptyKey             = new self('empty', 'empty');
        $emptyKey->contents   = '';
        $emptyKey->passphrase = '';

        return $emptyKey;
    }

    /** @param non-empty-string $contents */
    public static function plainText(string $contents, string $passphrase = ''): self
    {
        return new self($contents, $passphrase);
    }

    /** @param non-empty-string $contents */
    public static function base64Encoded(string $contents, string $passphrase = ''): self
    {
        $decoded = SodiumBase64Polyfill::base642bin(
            $contents,
            SodiumBase64Polyfill::SODIUM_BASE64_VARIANT_ORIGINAL
        );

        // @phpstan-ignore-next-line
        return new self($decoded, $passphrase);
    }

    /** @throws FileCouldNotBeRead */
    public static function file(string $path, string $passphrase = ''): self
    {
        try {
            $file = new SplFileObject($path);
        } catch (Throwable $exception) {
            throw FileCouldNotBeRead::onPath($path, $exception);
        }

        $contents = $file->fread($file->getSize());
        assert(is_string($contents));
        assert($contents !== '');

        return new self($contents, $passphrase);
    }

    public function contents(): string
    {
        return $this->contents;
    }

    public function passphrase(): string
    {
        return $this->passphrase;
    }
}
