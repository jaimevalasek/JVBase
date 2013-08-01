<?php
 
namespace JVBase\Filter;
 
use Zend\Validator\AbstractValidator;
 
class Identical extends AbstractValidator
{
    const DIFFERENT_FROM = 'DIFFERENT_FROM';
 
    protected $messageTemplates = array(
        self::DIFFERENT_FROM => 'Os valores dos campos não são iguais',
    );
 
    private $field;
 
    public function __construct(array $options = array())
    {
        if (!isset($options['field'])) {
            throw new Exception\InvalidArgumentException('Campo para verificação');
        }
 
        $this->field = $options['field'];
 
        parent::__construct($options);
    }
 
    public function isValid($value, $context = null)
    {
        if (!is_array($context) or !isset($context[$this->field])) {
            throw new Exception\RuntimeException(sprintf('O campo "%s" não é igual ao valor ', $this->field));
        }
 
        $this->setValue($value);
 
        if ($value !== $context[$this->field]) {
            $this->error(self::DIFFERENT_FROM);
 
            return false;
        }
 
        return true;
    }
}