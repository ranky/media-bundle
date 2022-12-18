<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\FileManipulation\Compression;

use Psr\Log\LoggerInterface;
use Ranky\MediaBundle\Domain\Contract\FileCompressInterface;
use Ranky\MediaBundle\Domain\Contract\FileRepositoryInterface;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\Optimizers\Cwebp;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Svgo;

final class SpatieFileCompression implements FileCompressInterface
{

    public function __construct(
        private readonly int $imageQuality,
        private readonly LoggerInterface $logger,
        private readonly FileRepositoryInterface $fileRepository
    ) {
    }


    public function support(File $file): bool
    {
        return \str_contains($file->mime(), 'image/');
    }

    public function compress(string $absolutePath): void
    {
        // For each megabyte I reduce the percentage of image quality by 5%
        $reduceQuality = 5;
        $filesize      = $this->fileRepository->filesizeFromPath($absolutePath);
        $mb            = (int)round($filesize / 1048576 /* Bytes to Mb */);
        $quality       = $mb >= 1 ? ($this->imageQuality - ($mb * $reduceQuality)) : $this->imageQuality;

        $jpegoptim = new Jpegoptim(
            [
                '--max='.$quality,
                '--strip-all',
                '--all-progressive',
            ]
        );
        $pngquant = new Pngquant(
            [
                '--quality='.$quality, // Fails when it does not reach the required minimum
                '--force',
                '--skip-if-larger',
                '--verbose',
            ]
        );
        $optipng  = new Optipng(
            [
                '-i0',
                '-o2',
                '-quiet',
            ]
        );

        $svgo = new Svgo([
            '--disable={cleanupIDs,removeViewBox}',
        ]);

        $gifsicle = new Gifsicle([
            //'-b',
            '--optimize=3',
            '--lossy=80',
        ]);

        $webp = new Cwebp([
            '-m 6',
            '-pass 10',
            '-mt',
            '-q '.$quality,
        ]);


        $optimizerChain = OptimizerChainFactory::create()
            ->useLogger($this->logger)
            ->addOptimizer($jpegoptim)
            ->addOptimizer($pngquant)
            ->addOptimizer($optipng)
            ->addOptimizer($svgo)
            ->addOptimizer($gifsicle)
            ->addOptimizer($webp);

        $optimizerChain->optimize($absolutePath);
    }
}
