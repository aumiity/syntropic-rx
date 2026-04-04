<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabelSetting extends Model
{
    public const DEFAULT_ROW_STYLES = [
        'shop_name' => ['fontSize' => 13, 'bold' => true, 'italic' => false, 'underline' => false, 'align' => 'left'],
        'date' => ['fontSize' => 10, 'bold' => false, 'italic' => false, 'underline' => false, 'align' => 'right'],
        'address' => ['fontSize' => 10, 'bold' => false, 'italic' => false, 'underline' => false, 'align' => 'left'],
        'product_name' => ['fontSize' => 14, 'bold' => true, 'italic' => false, 'underline' => false, 'align' => 'left'],
        'dosage' => ['fontSize' => 16, 'bold' => true, 'italic' => false, 'underline' => false, 'align' => 'left'],
        'indication' => ['fontSize' => 10, 'bold' => false, 'italic' => false, 'underline' => false, 'align' => 'left'],
        'advice' => ['fontSize' => 10, 'bold' => false, 'italic' => false, 'underline' => false, 'align' => 'left'],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'paper_width',
        'paper_height',
        'padding_top',
        'padding_right',
        'padding_bottom',
        'padding_left',
        'font_family',
        'bold_shop',
        'bold_product',
        'bold_dosage',
        'line_spacing',
        'section_gap',
        'row_styles',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bold_shop' => 'boolean',
        'bold_product' => 'boolean',
        'bold_dosage' => 'boolean',
        'line_spacing' => 'float',
        'row_styles' => 'array',
    ];

    /**
     * Get the current label settings (always id=1).
     * If not exists, create with default values.
     *
     * @return \App\Models\LabelSetting
     */
    public static function current()
    {
        $setting = self::firstOrCreate(
            ['id' => 1],
            [
                'paper_width' => 100,
                'paper_height' => 75,
                'padding_top' => 3,
                'padding_right' => 3,
                'padding_bottom' => 3,
                'padding_left' => 3,
                'font_family' => 'Tahoma',
                'bold_shop' => true,
                'bold_product' => true,
                'bold_dosage' => true,
                'line_spacing' => 1.4,
                'section_gap' => 4,
                'row_styles' => self::DEFAULT_ROW_STYLES,
            ]
        );

        $savedRowStyles = is_array($setting->row_styles) ? $setting->row_styles : [];
        $normalized = [];

        foreach (self::DEFAULT_ROW_STYLES as $key => $defaults) {
            $incoming = is_array($savedRowStyles[$key] ?? null) ? $savedRowStyles[$key] : [];
            $normalized[$key] = array_merge($defaults, $incoming);
        }

        $setting->row_styles = $normalized;
        return $setting;
    }
}
