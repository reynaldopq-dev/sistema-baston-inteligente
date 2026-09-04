<?php

return [
    'required'         => 'El campo :attribute es obligatorio.',
    'string'           => 'El campo :attribute debe ser texto.',
    'unique'           => 'El :attribute ya está registrado.',
    'date'             => 'El campo :attribute debe ser una fecha válida.',
    'in'               => 'El valor seleccionado en :attribute no es válido.',
    'email'            => 'El campo :attribute debe ser un correo válido.',
    'image'            => 'El campo :attribute debe ser una imagen válida.',
    'mimes'            => 'El campo :attribute debe ser de tipo: :values.',
    'integer'          => 'El campo :attribute debe ser un número entero.',
    'min'              => [
        'string'  => 'El campo :attribute debe tener al menos :min caracteres.',
        'integer' => 'El campo :attribute debe ser al menos :min.',
    ],
    'max'              => [
        'string'  => 'El campo :attribute no debe exceder :max caracteres.',
        'integer' => 'El campo :attribute no debe ser mayor a :max.',
        'file'    => 'El archivo :attribute no debe ser mayor a :max kilobytes.',
    ],
    'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'nullable'         => '',
    'regex'            => 'El formato del campo :attribute no es válido.',
    'digits_between'   => 'El campo :attribute debe contener solo números, entre :min y :max dígitos.',
    'before_or_equal'  => 'El campo :attribute no puede ser una fecha futura.',
    'after'            => 'El campo :attribute no es una fecha válida.',
    'before'           => 'El campo :attribute no cumple con la fecha mínima requerida.',
    'exists'           => 'El :attribute seleccionado no es válido.',
    'confirmed'        => 'La confirmación de :attribute no coincide.',
    'required_if'      => 'El campo :attribute es obligatorio cuando :other es :value.',
    'password'         => [
        'letters'       => 'El campo :attribute debe contener al menos una letra.',
        'mixed'         => 'El campo :attribute debe contener al menos una mayúscula y una minúscula.',
        'numbers'       => 'El campo :attribute debe contener al menos un número.',
        'symbols'       => 'El campo :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'La :attribute ingresada aparece en una filtración de datos conocida. Elige otra.',
    ],

    // Mensajes específicos por campo + regla (tienen prioridad sobre los genéricos de arriba)
    'custom' => [
        'nombres' => [
            'regex' => 'El campo nombres solo puede contener letras y espacios simples entre palabras, sin números, caracteres especiales, ni espacios dobles, al inicio o al final.',
        ],
        'apellido_paterno' => [
            'regex' => 'El apellido paterno solo puede contener letras y espacios simples entre palabras, sin números, caracteres especiales, ni espacios dobles, al inicio o al final.',
        ],
        'apellido_materno' => [
            'regex' => 'El apellido materno solo puede contener letras y espacios simples entre palabras, sin números, caracteres especiales, ni espacios dobles, al inicio o al final.',
        ],
        'ci' => [
            'regex' => 'El carnet de identidad solo puede contener letras, números y guiones, sin otros caracteres especiales.',
        ],
        'telefono' => [
            'digits_between' => 'El teléfono debe contener solo números, entre :min y :max dígitos.',
        ],
        'direccion' => [
            'regex' => 'La dirección contiene caracteres no permitidos, o tiene espacios dobles, al inicio o al final.',
        ],
        'marca' => [
            'regex' => 'La marca solo puede contener letras, números y espacios simples entre palabras, sin espacios dobles, al inicio o al final.',
        ],
        'modelo' => [
            'regex' => 'El modelo solo puede contener letras, números y espacios simples entre palabras, sin espacios dobles, al inicio o al final.',
        ],
        'codigo' => [
            'regex' => 'El código solo puede contener letras, números y guiones, sin otros caracteres especiales.',
        ],
        'numero_serie' => [
            'regex' => 'El número de serie solo puede contener letras, números y guiones, sin otros caracteres especiales.',
        ],
        'fecha_nacimiento' => [
            'before_or_equal' => 'La fecha de nacimiento no puede ser una fecha futura.',
            'after'            => 'La fecha de nacimiento no es una fecha real.',
            'before'           => 'Debe ser mayor de edad (18 años) para registrarse como usuario del sistema.',
        ],
        'fecha_adquisicion' => [
            'before_or_equal' => 'La fecha de adquisición no puede ser una fecha futura.',
            'after'            => 'La fecha de adquisición no es válida.',
        ],
        'diagnostico' => [
            'in' => 'Seleccione un diagnóstico válido de la lista.',
        ],
        'rol' => [
            'in' => 'Seleccione un rol válido de la lista.',
        ],
        'name' => [
            'regex' => 'El nombre solo puede contener letras y espacios simples entre palabras, sin números, caracteres especiales, ni espacios dobles, al inicio o al final.',
        ],
    ],

    'attributes' => [
        'nombres'          => 'nombres',
        'apellido_paterno' => 'apellido paterno',
        'apellido_materno' => 'apellido materno',
        'ci'               => 'carnet de identidad',
        'fecha_nacimiento' => 'fecha de nacimiento',
        'telefono'         => 'teléfono',
        'direccion'        => 'dirección',
        'diagnostico'      => 'diagnóstico',
        'estado'           => 'estado',
        'foto'             => 'foto',
        'correo'           => 'correo',
        'rol'              => 'rol',
        'codigo'           => 'código',
        'marca'            => 'marca',
        'modelo'           => 'modelo',
        'numero_serie'     => 'número de serie',
        'fecha_adquisicion'=> 'fecha de adquisición',
        'bateria'          => 'batería',
        'name'             => 'nombre',
        'email'            => 'correo electrónico',
        'password'         => 'contraseña',
    ],
];