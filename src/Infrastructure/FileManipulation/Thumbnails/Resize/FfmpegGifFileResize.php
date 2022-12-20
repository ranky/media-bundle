<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\FileManipulation\Thumbnails\Resize;

use Psr\Log\LoggerInterface;
use Ranky\MediaBundle\Domain\Contract\FileResizeInterface;
use Ranky\MediaBundle\Domain\Enum\GifResizeDriver;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class FfmpegGifFileResize implements FileResizeInterface
{


    public function __construct(
        private readonly ?string $imageResizeGifDriver,
        private readonly LoggerInterface $logger
    ) {
    }

    public function resize(string $inputPath, string $outputPath, Dimension $dimension): bool
    {
        $command = <<<END
            ffmpeg -y -i $inputPath
            -filter_complex
            "fps=5,scale={$dimension->width()}:-1:flags=lanczos,split[s0][s1];[s0]palettegen=max_colors=256[p];[s1][p]paletteuse=dither=bayer"
            $outputPath
            END;

        $loggerContext = [
            'with'   => $dimension->width(),
            'input'  => $inputPath,
            'output' => $inputPath,
        ];
        $this->logger->info('Start ffmpeg gif resize', $loggerContext);
        $timeStart = \microtime(true);
        $ffmpeg    = Process::fromShellCommandline(\str_replace(["\r", "\n"], ' ', $command));
        $ffmpeg->run();

        if (!$ffmpeg->isSuccessful()) {
            $exception = new ProcessFailedException($ffmpeg);
            $this->logger->error($exception->getMessage(), $loggerContext);
            throw $exception;
        }

        $this->logger->info(
            'Finish ffmpeg gif resize',
            [...$loggerContext, ...['time' => \microtime(true) - $timeStart.' seconds']]
        );

        return true;
    }

    public function support(File $file): bool
    {
        return $file->extension() === 'gif' && $this->imageResizeGifDriver === GifResizeDriver::FFMPEG->value;
    }
}
