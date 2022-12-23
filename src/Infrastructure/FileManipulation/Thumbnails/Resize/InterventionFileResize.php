<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\FileManipulation\Thumbnails\Resize;

use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Psr\Log\LoggerInterface;
use Ranky\MediaBundle\Domain\Contract\FileResizeInterface;
use Ranky\MediaBundle\Domain\Enum\ImageResizeDriver;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\File;

final class InterventionFileResize implements FileResizeInterface
{

    public function __construct(private readonly string $resizeImageDriver, private readonly LoggerInterface $logger)
    {
    }

    /**
     * @throws \Throwable
     */
    public function resize(string $inputPath, string $outputPath, Dimension $dimension): bool
    {
        $loggerContext = [
            'class' => self::class,
            'input' => $inputPath,
            'output' => $outputPath,
            'dimension' => $dimension->toArray(),
        ];

        if ($this->resizeImageDriver === ImageResizeDriver::GD->value && !\extension_loaded('exif')) {
            $this->logger->warning('GD driver require exif extension to be installed', $loggerContext);

            return false;
        }

        $extension = \pathinfo($inputPath, PATHINFO_EXTENSION);
        if ($this->resizeImageDriver === ImageResizeDriver::IMAGICK->value &&
            !\in_array(\mb_strtoupper($extension), \Imagick::queryFormats(), true))
        {
            $this->logger->warning(
                \sprintf('The %s extension is not supported by the current Imagick installation.', $extension),
                $loggerContext
            );

            return false;
        }

        if ($this->resizeImageDriver === ImageResizeDriver::GD->value)
        {
            $gdInfo = \gd_info();

            if ($extension === 'webp' && (!\array_key_exists('WebP Support', $gdInfo) || !$gdInfo['WebP Support'] === true)){
                $this->logger->warning('WebP is not supported by the current GD installation.', $loggerContext);
                return false;
            }
            if ($extension === 'avif' && (!\array_key_exists('AVIF Support', $gdInfo) || !$gdInfo['AVIF Support'] === true)){
                $this->logger->warning('AVIF is not supported by the current GD installation.', $loggerContext);
                return false;
            }
            if ($extension === 'bmp' && (!\array_key_exists('WBMP Support', $gdInfo) || !$gdInfo['WBMP Support'] === true)){
                $this->logger->warning('WBMP is not supported by the current GD installation.', $loggerContext);
                return false;
            }

        }

        $this->logger->info('Start Intervention Image resizes', $loggerContext);
        if (!$dimension->width()) {
            $this->logger->info(
                'The Image did not resize, because it does not have the width dimension.',
                $loggerContext
            );

            return false;
        }
        $timeStart = \microtime(true);
        $manager   = new ImageManager(['driver' => $this->resizeImageDriver]);
        $image     = $manager->make($inputPath);
        $image->orientate();
        if (null !== $dimension->height()) {
            $image->fit($dimension->width(), $dimension->height(), function (Constraint $constraint): void {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } else {
            $image->widen($dimension->width(), function (Constraint $constraint): void {
                $constraint->upsize();
            });
        }

        try {
            $image->save($outputPath);
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), $loggerContext);
            throw $exception;
        }

        $this->logger->info(
            'Finish Intervention Image resizes',
            [...$loggerContext, ...['time' => \microtime(true) - $timeStart.' seconds']]
        );

        return true;
    }


    public function support(File $file): bool
    {
        $resizeDriver = ImageResizeDriver::from($this->resizeImageDriver);

        return \in_array($file->extension(), $resizeDriver->supportedFormats(), true) &&
            $file->extension() !== 'gif';
    }
}
