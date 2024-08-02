<?php

namespace Spatie\Mailcoach\Domain\Campaign\Rules;

use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Spatie\Mailcoach\Domain\Content\Actions\CreateDomDocumentFromHtmlAction;

class HtmlRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_array($value)) {
            $value = $value['html'];
        }

        try {
            app(CreateDomDocumentFromHtmlAction::class)->execute($value, false);
        } catch (Exception $exception) {
            $message = $this->getMessage($exception, $value);

            $fail($message);
        }
    }

    public function getMessage(Exception $exception, mixed $value): string
    {
        preg_match('/Tag (.*) invalid in Entity.*/', $exception->getMessage(), $match);

        if (isset($match[1])) {
            return __mc('Your HTML contains a &lt;:tag&gt; tag which is not supported in a lot of mail clients.', [
                'tag' => $match[1],
            ]);
        }

        preg_match('/line: (.*)/', $exception->getMessage(), $match);

        $line = $match[1] ?? null;
        if ($line) {
            $lines = explode("\n", $value);

            $code = trim($lines[$line - 1] ?? $lines[$line]);
        }

        $message = str_replace('DOMDocument::loadHTML(): ', '', $exception->getMessage());

        if (isset($code)) {
            $code = htmlentities($code);
            $message .= "<pre><code class='markup-code mt-2'>{$code}</code></pre>";
        }

        return ucfirst($message);
    }
}
