<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'o :attribute deve ser aceito.',
    'active_url' => 'o :attribute não é um URL válido.',
    'after' => 'o :attribute deve ser uma data depois :date.',
    'after_or_equal' => 'o :attribute deve ser uma data posterior ou igual a :date.',
    'alpha' => 'o :attribute só pode conter letras.',
    'alpha_dash' => 'o :attribute só pode conter letras, números, travessões e sublinhados.',
    'alpha_num' => 'o :attribute só pode conter letras e números.',
    'array' => 'o :attribute deve ser uma matriz.',
    'before' => 'o :attribute deve ser uma data antes :date.',
    'before_or_equal' => 'o :attribute deve ser uma data anterior ou igual a :date.',
    'between' => [
        'numeric' => 'o :attribute deve estar entre :min e :max.',
        'file' => 'o :attribute deve estar entre :min e :max kilobytes.',
        'string' => 'o :attribute deve estar entre :min e :max personagens.',
        'array' => 'o :attribute deve ter entre :min e :max Itens.',
    ],
    'boolean' => 'o :attribute campo deve ser verdadeiro ou falso.',
    'confirmed' => 'o :attribute a confirmação não corresponde.',
    'date' => 'o :attribute não é uma data válida.',
    'date_equals' => 'o :attribute deve ser uma data igual a :date.',
    'date_format' => 'o :attribute não corresponde ao formato :format.',
    'different' => 'o :attribute e :other deve ser diferente.',
    'digits' => 'o :attribute devemos ser :digits dígitos.',
    'digits_between' => 'o :attribute deve estar entre :min e :max dígitos.',
    'dimensions' => 'o :attribute tem dimensões de imagem inválidas.',
    'distinct' => 'o :attribute campo tem um valor duplicado.',
    'email' => 'o :attribute Deve ser um endereço de e-mail válido.',
    'ends_with' => 'o :attribute deve terminar com um dos seguintes: :values.',
    'exists' => 'O selecionado :attribute é inválido.',
    'file' => 'o :attribute deve ser um arquivo.',
    'filled' => 'o :attribute campo deve ter um valor.',
    'gt' => [
        'numeric' => 'o :attribute deve ser maior que :value.',
        'file' => 'o :attribute deve ser maior que :value kilobytes.',
        'string' => 'o :attribute deve ser maior que :value personagens.',
        'array' => 'o :attribute deve ter mais que :value Itens.',
    ],
    'gte' => [
        'numeric' => 'o :attribute deve ser maior ou igual :value.',
        'file' => 'o :attribute deve ser maior ou igual :value kilobytes.',
        'string' => 'o :attribute deve ser maior ou igual :value personagens.',
        'array' => 'o :attribute deve ter :value itens ou mais.',
    ],
    'image' => 'o :attribute deve ser uma imagem.',
    'in' => 'O selecionado :attribute é inválido.',
    'in_array' => 'o :attribute campo não existe em :other.',
    'integer' => 'o :attribute deve ser um número inteiro.',
    'ip' => 'o :attribute deve ser um endereço IP válido.',
    'ipv4' => 'o :attribute deve ser um endereço IPv4 válido.',
    'ipv6' => 'o :attribute deve ser um endereço IPv6 válido.',
    'json' => 'o :attribute deve ser uma string JSON válida.',
    'lt' => [
        'numeric' => 'o :attribute deve ser menor que :value.',
        'file' => 'o :attribute deve ser menor que :value kilobytes.',
        'string' => 'o :attribute deve ser menor que :value personagens.',
        'array' => 'o :attribute deve ter menos que :value Itens.',
    ],
    'lte' => [
        'numeric' => 'o :attribute deve ser menor ou igual :value.',
        'file' => 'o :attribute deve ser menor ou igual :value kilobytes.',
        'string' => 'o :attribute deve ser menor ou igual :value personagens.',
        'array' => 'o :attribute não deve ter mais que :value Itens.',
    ],
    'max' => [
        'numeric' => 'o :attribute não pode ser maior que :max.',
        'file' => 'o :attribute não pode ser maior que :max kilobytes.',
        'string' => 'o :attribute não pode ser maior que :max personagens.',
        'array' => 'o :attribute não pode ter mais que :max Itens.',
    ],
    'mimes' => 'o :attribute deve ser um arquivo do tipo: :values.',
    'mimetypes' => 'o :attribute deve ser um arquivo do tipo: :values.',
    'min' => [
        'numeric' => 'o :attribute deve ser pelo menos :min.',
        'file' => 'o :attribute deve ser pelo menos :min kilobytes.',
        'string' => 'o :attribute deve ser pelo menos :min personagens.',
        'array' => 'o :attribute deve ter pelo menos :min Itens.',
    ],
    'not_in' => 'o selecionado :attribute é inválido.',
    'not_regex' => 'o :attribute formato é inválido.',
    'numeric' => 'o :attribute deve ser um número.',
    'password' => 'A senha está incorreta.',
    'present' => 'o :attribute campo deve estar presente.',
    'regex' => 'o :attribute formato é inválido.',
    'required' => 'o :attribute campo é obrigatório.',
    'required_if' => 'o :attribute campo é obrigatório quando :other é :value.',
    'required_unless' => 'o :attribute campo é obrigatório a menos :other é em :values.',
    'required_with' => 'o :attribute campo é obrigatório quando :values é presente.',
    'required_with_all' => 'o :attribute campo é obrigatório quando :values estão presentes.',
    'required_without' => 'o :attribute campo é obrigatório quando :values não está presente.',
    'required_without_all' => 'o :attribute campo é obrigatório quando nenhum :values estão presentes.',
    'same' => 'o :attribute e :other deve combinar.',
    'size' => [
        'numeric' => 'o :attribute devemos ser :size.',
        'file' => 'o :attribute devemos ser :size kilobytes.',
        'string' => 'o :attribute devemos ser :size personagens.',
        'array' => 'o :attribute deve conter :size Itens.',
    ],
    'starts_with' => 'o :attribute deve começar com um dos seguintes: :values.',
    'string' => 'o :attribute deve ser uma string.',
    'timezone' => 'o :attribute deve ser uma zona válida.',
    'unique' => 'o :attribute já foi tomada.',
    'uploaded' => 'o :attribute Falha ao carregar.',
    'url' => 'o :attribute formato é inválido.',
    'uuid' => 'o :attribute deve ser um UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],
    'ncm' => [ 
        'invalid' => 'Número NCM inválido',
        'required' => 'Número NCM necessário',
    ],
    'invalid_number' => 'O número deve estar no formato internacional do :country',
    'invalid_zipcode' => 'O cep :input não foi encontrado para o estado selecionado. verifique o estado e o cep novamente. :link',
    'Message' => "entre em contato com o atendimento ao cliente Homedeliverybr para obter assistência",

];
