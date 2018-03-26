<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\Utils;
use Intervention\Image\AbstractFont;
use Intervention\Image\Image;

/**
 * Class DefaultTheme.
 */
class DefaultTheme extends AbstractTheme implements PositionAwareThemeInterface
{
    /**
     * Apply theme.
     *
     * @param Image $image
     * @param int   $width
     * @param int   $height
     *
     * @return Image
     */
    public function apply(Image $image, int $width, int $height): Image
    {
        $w = $width;
        $h = $height;

        $jobTitle = $this->request->get('jobTitle');
        $jobDescription = $this->request->get('jobDescription');

        $avatarInput = $this->request->get('avatar');
        $avatarImageUrl = $avatarInput['image'] ?? null;
        $avatarName = $avatarInput['name'] ?? null;
        $avatarContact = $avatarInput['contact'] ?? null;
        $usesAvatar = ($avatarInput && $avatarImageUrl && $avatarName && $avatarContact);

        $rectW = $w / 3;
        $rectH = $usesAvatar ? (int)floor($h * 0.6) : $h;
        $image->rectangle(0, 0, $rectW, $rectH, function ($draw) {
            $draw->background('rgba(255, 255, 255, 0.8)');
        });

        $logoPath = realpath('images/hd-horizontal-watermark-300w.png');
        $logoInfo = Utils::getImageInfo($logoPath);

        $padLeft = 30;
        $padTop = 30;

        $logoPadLeft = $padLeft;
        $logoPadTop = $padTop;
        $image->insert($logoInfo['path'], 'top-left', $logoPadLeft, $logoPadTop);

        $textPadLeft = $padLeft;
        $titlePadTop = $padTop * 3 + $logoInfo['height'] / 2;
        $titleSize = 24;

        if (null !== $jobTitle) {
            $image->text(Utils::wordwrap($jobTitle, 25), $textPadLeft, $titlePadTop, function (AbstractFont $font) use ($titleSize) {
                $font->file('fonts/SourceSansPro-Bold.otf');
                $font->size($titleSize);
                $font->color('rgb(43, 57, 132)');
                $font->align('left');
                $font->valign('top');
            });
        }

        $descSize = 16;
        $descPadTop = $titlePadTop - 10;

        if (null !== $jobDescription) {
            $image->text(Utils::wordwrap(Utils::truncate($jobDescription)), $textPadLeft, $descPadTop, function (AbstractFont $font) use ($descSize) {
                $font->file('fonts/SourceSansPro-Regular.otf');
                $font->size($descSize);
                $font->color('#000000');
                $font->align('left');
                $font->valign('top');
            });
        }

        if ($usesAvatar) {
            $blueTopLeftX = 0;
            $blueTopLeftY = $h;
            $blueBottomRightX = $rectW;
            $blueBottomRightY = abs((int)floor($h * 1.6) - $h);
            $image->rectangle($blueTopLeftX, $blueTopLeftY, $blueBottomRightX, $blueBottomRightY, function ($draw) {
                $draw->background('rgba(27, 179, 219, 0.8)'); // light blue
            });

            $avatarW = 120;
            $avatarH = 120;

            $nameTextSize = 20;
            $contactTextSize = 18;

            $avatarPadLeft = (int)floor(($rectW - $avatarW) / 2);
            $avatarPadTop = $blueBottomRightY + 20;
            $textPadLeft = $avatarPadLeft + (int)floor($avatarW / 2);
            $namePadTop = $avatarPadTop + $avatarH + 20;
            $contactPadTop = $namePadTop + $nameTextSize + 20;

            $avatarInfo = Utils::getImageInfo($avatarImageUrl);
            $avatar = $this->manager
                ->make($avatarInfo['path'])
                ->resize($avatarW, $avatarH);

            $image->insert($avatar, 'top-left', $avatarPadLeft, $avatarPadTop);
            $image->rectangle($avatarPadLeft, $avatarPadTop, $avatarPadLeft + $avatarW, $avatarPadTop + $avatarH, function ($draw) {
                $draw->border(2, 'rgb(43, 57, 132)');
            });

            $image->text($avatarName, $textPadLeft, $namePadTop, function (AbstractFont $font) use ($nameTextSize) {
                $font->file('fonts/SourceSansPro-Bold.otf');
                $font->size($nameTextSize);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $email = $avatarContact['email'] ?? null;
            if ($email) {
                $image->text($email, $textPadLeft, $contactPadTop, function (AbstractFont $font) use ($contactTextSize) {
                    $font->file('fonts/SourceSansPro-Regular.otf');
                    $font->size($contactTextSize);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('top');
                });
                $contactPadTop = $contactPadTop + $contactTextSize + 5;
            }

            $phone = $avatarContact['phone'] ?? null;
            if ($phone) {
                $image->text($phone, $textPadLeft, $contactPadTop, function (AbstractFont $font) use ($contactTextSize) {
                    $font->file('fonts/SourceSansPro-Regular.otf');
                    $font->size($contactTextSize);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('top');
                });
                $contactPadTop = $contactPadTop + $contactTextSize + 5;
            }

            $skype = $avatarContact['skype'] ?? null;
            if ($skype) {
                $image->text('Skype: '.$skype, $textPadLeft, $contactPadTop, function (AbstractFont $font) use ($contactTextSize) {
                    $font->file('fonts/SourceSansPro-Regular.otf');
                    $font->size($contactTextSize);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('top');
                });
            }
        }

        return $image;
    }

    /**
     * @inheritdoc
     */
    public function supportsRandomization(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return PositionAwareThemeInterface::POSITION_LEFT;
    }
}