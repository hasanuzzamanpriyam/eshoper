<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    /**
     * Get the dynamic value for specific settings.
     *
     * @param string $value
     * @return string
     */
    public function getValueAttribute($value)
    {
        if ($this->type == 'company_copyright_text') {
            return str_replace('{{year}}', date('Y'), preg_replace('/\b\d{4}\b/', date('Y'), $value));
        }
        return $value;
    }
}
