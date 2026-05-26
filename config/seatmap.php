<?php

return [
    'presets' => [
        'A320' => [
            'name' => 'Airbus A320',
            'letters' => ['A', 'B', 'C', 'D', 'E', 'F'],
            'aisle_after' => ['C'],
            'rows' => [
                ['start' => 1, 'end' => 3, 'class' => 'business'],
                ['start' => 4, 'end' => 30, 'class' => 'economy', 'exit_rows' => [12, 14]]
            ]
        ],
        'B737' => [
            'name' => 'Boeing 737-800',
            'letters' => ['A', 'B', 'C', 'D', 'E', 'F'],
            'aisle_after' => ['C'],
            'rows' => [
                ['start' => 1, 'end' => 4, 'class' => 'business'],
                ['start' => 5, 'end' => 32, 'class' => 'economy', 'exit_rows' => [15, 16]]
            ]
        ],
        'ATR72' => [
            'name' => 'ATR 72-600',
            'letters' => ['A', 'B', 'C', 'D'],
            'aisle_after' => ['B'],
            'rows' => [
                ['start' => 1, 'end' => 18, 'class' => 'economy', 'exit_rows' => [1, 2]]
            ]
        ]
    ]
];
