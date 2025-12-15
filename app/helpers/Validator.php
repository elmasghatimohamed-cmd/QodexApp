<?php

namespace App\Helpers;
class Validator
{
    private $errors = [];
    public function validate($data, $rules)
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $rulesArray = is_array($ruleSet) ? $ruleSet : explode('|', $ruleSet);

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule, $data);
            }
        }

        return empty($this->errors);
    }
    private function applyRule($field, $value, $rule, $allData)
    {
        if (strpos($rule, ':') !== false) {
            [$rule, $param] = explode(':', $rule, 2);
        }

        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->errors[$field][] = "Le champ {$field} est requis";
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "Le champ {$field} doit être un email valide";
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < (int) $param) {
                    $this->errors[$field][] = "Le champ {$field} doit contenir au moins {$param} caractères";
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > (int) $param) {
                    $this->errors[$field][] = "Le champ {$field} ne doit pas dépasser {$param} caractères";
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = "Le champ {$field} doit être numérique";
                }
                break;

            case 'integer':
                if (!empty($value) && !is_int($value) && !ctype_digit($value)) {
                    $this->errors[$field][] = "Le champ {$field} doit être un entier";
                }
                break;

            case 'in':
                $allowed = explode(',', $param);
                if (!empty($value) && !in_array($value, $allowed)) {
                    $this->errors[$field][] = "Le champ {$field} doit être l'une des valeurs: " . implode(', ', $allowed);
                }
                break;

            case 'exists':
                // Format: exists:table,column
                [$table, $column] = explode(',', $param);
                // Cette règle nécessite une connexion DB, à implémenter si nécessaire
                break;

            case 'unique':
                // Format: unique:table,column
                [$table, $column] = explode(',', $param);
                // Cette règle nécessite une connexion DB, à implémenter si nécessaire
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if (!isset($allData[$confirmField]) || $value !== $allData[$confirmField]) {
                    $this->errors[$field][] = "Le champ {$field} ne correspond pas à la confirmation";
                }
                break;
        }
    }
    public function getErrors()
    {
        return $this->errors;
    }
    public function getFieldErrors($field)
    {
        return $this->errors[$field] ?? [];
    }
    public function hasErrors()
    {
        return !empty($this->errors);
    }
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    public static function validateInteger($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
    public static function validateFloat($value)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }
    public static function sanitizeString($value)
    {
        return trim(strip_tags($value));
    }
    public static function validateUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}

