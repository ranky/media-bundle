<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\FileManipulation\Resize;

use Psr\Log\LoggerInterface;
use Ranky\MediaBundle\Domain\Contract\FileResize;
use Ranky\MediaBundle\Domain\Enum\GifResizeDriver;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\File;

final class ImagickGifFileResize implements FileResize
{

    public function __construct(
        private readonly ?string $imageResizeGifDriver,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws \ImagickException
     */
    public function resize(string $inputPath, string $outputPath, Dimension $dimension): bool
    {
        /*
         $height   ??= '';
         system("convert $path -coalesce -resize {$width}x{$height} -deconstruct $newPath");
         return;
        */
        if (!$dimension->width()) {
            $this->logger->warning('Gif image did not resize, because it does not have the width dimension.', [
                'input' => $inputPath,
                'output' => $outputPath,
                'dimension' => $dimension->toArray(),
            ]);
            return false;
        }

        $loggerContext = [
            'with'   => $dimension->width(),
            'input'  => $inputPath,
            'output' => $outputPath,
        ];
        $this->logger->info('Start Imagick gif resize', $loggerContext);
        $timeStart = \microtime(true);

        $gifImage = new \Imagick($inputPath);
        $gifImage = $gifImage->coalesceImages();
        foreach ($gifImage as $frame) {
            $frame->thumbnailImage($dimension->width(), $dimension->height() ?? 0);
            $frame->setImagePage($dimension->width(), $dimension->height() ?? 0, 0, 0);
        }

        try {
            $gifImage = $gifImage->deconstructImages();
            $gifImage->writeImages($outputPath, true);
            $gifImage->clear();
        } catch (\Throwable $exception) {
            $exception = new \RuntimeException($exception->getMessage());
            $this->logger->error($exception->getMessage(), [
                'input' => $inputPath,
                'output' => $outputPath,
                'dimension' => $dimension->toArray(),
            ]);
            throw $exception;
        }

        $this->logger->info(
            'Finish Imagick gif resize',
            [...$loggerContext, ...['time' => \microtime(true) - $timeStart.' seconds']]
        );

        return true;
    }

    public function support(File $file): bool
    {
        return $file->extension() === 'gif' && $this->imageResizeGifDriver === GifResizeDriver::IMAGICK->value;
    }
}
