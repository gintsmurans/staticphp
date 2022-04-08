<?php

namespace System\Modules\Core\Exceptions;

use System\Modules\Core\Models\Load;

/**
 * Global ErrorMessage Exception for throwing and catching custom errors
 */
class ErrorMessage extends \Exception
{
    public ?string $description = null;
    public string $outputType = ErrorMessage::OUTPUT_TYPE_PLAIN;
    public int $httpStatusCode = 200;
    public bool $includeHtmlTemplate = false;

    public const OUTPUT_TYPE_PLAIN = 'plain';
    public const OUTPUT_TYPE_HTML = 'html';
    public const OUTPUT_TYPE_JSON = 'json';
    public const OUTPUT_TYPE_XML = 'xml';

    public function __construct(
        string $message = '',
        int $code = 0,
        ?string $description = null,
        string $outputType = ErrorMessage::OUTPUT_TYPE_PLAIN,
        int $httpStatusCode = 200,
        bool $includeHtmlTemplate = false
    ) {
        $this->description = $description;
        $this->outputType = $outputType;
        $this->httpStatusCode = $httpStatusCode;
        $this->includeHtmlTemplate = $includeHtmlTemplate;

        parent::__construct($message, $code);
    }

    public function formatMessage(): string
    {
        if ($this->outputType === ErrorMessage::OUTPUT_TYPE_HTML) {
            return "{$this->code} {$this->message}<br /><br />{$this->description}";
        }

        if ($this->outputType === ErrorMessage::OUTPUT_TYPE_JSON) {
            return json_encode([
                'message' => [
                    'code' => $this->code,
                    'title' => $this->message,
                    'description' => $this->description,
                ],
            ]);
        }

        if ($this->outputType === ErrorMessage::OUTPUT_TYPE_XML) {
            return <<<XML
<Message xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <Code>{$this->code}</Code>
    <Title>{$this->message}</Title>
    <Description>{$this->description}</Description>
</Message>
XML;
        }


        return "{$this->code} {$this->message}\n\n{$this->description}";
    }

    public function outputMessage(): void
    {
        $message = $this->formatMessage();
        if (headers_sent() == false && $this->httpStatusCode != 200) {
            header("HTTP/1.0 {$this->httpStatusCode} {$this->message}");
        }

        if ($this->outputType === ErrorMessage::OUTPUT_TYPE_PLAIN) {
            header('Content-Type:text/plain; charset=utf-8');
        } elseif ($this->outputType === ErrorMessage::OUTPUT_TYPE_JSON) {
            header('Content-Type:application/json; charset=utf-8');
        } elseif ($this->outputType === ErrorMessage::OUTPUT_TYPE_XML) {
            header('Content-Type:application/xml; charset=utf-8');
        } elseif ($this->outputType === ErrorMessage::OUTPUT_TYPE_HTML) {
            header('Content-Type:text/html; charset=utf-8');
        }

        if ($this->outputType === ErrorMessage::OUTPUT_TYPE_HTML && $this->includeHtmlTemplate) {
            $filename = 'Error';
            if ($this->httpStatusCode != 200) {
                $filename = "E{$this->httpStatusCode}";
            }
            $data = ['code' => $this->code, 'title' => $this->message, 'description' => $this->description];
            Load::view(["Errors/{$filename}.html"], $data);
        } else {
            echo $message;
        }
    }
}
