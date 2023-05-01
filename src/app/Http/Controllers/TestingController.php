<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class TestingController extends Controller
{
    public function store(Request $request)
    {
        return response()->json('invalid data', 400);
    }

    public function getDynamicValue($id)
    {
        return '300' . $id;
    }

    public function getTestChart()
    {
        return [
            [
                'label' => 'Sales',
                'backgroundColor' => '#21252D',
                'data' => [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11]
            ],
            [
                'label' => 'Purchase',
                'backgroundColor' => '#4466F2',
                'data' => [20, 40, 32, 49, 20, 50, 19, 30, 70, 20, 42, 21]
            ]
        ];
    }

    public function getTestValue(Request $request)
    {
        return [
            'req' => $request->all(),
            'data' => [
                [
                    'expandable_data' => 'Okay',
                    'invoice' => 1000,
                    'name' => 'Mr a',
                    'email' => 'admin@admin.com',
                    'age' => 30,
                    'status' => 'Active',
                    'status2' => 'Active',

                    "image" => '',
                    'created_by' => [
                        'id' => 1,
                        'first_name' => 'Super',
                        'last_name' => 'Admin',
                        'email' => 'admin@admin.com',
                    ],
                    "users" => [
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test'
                        ],
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test 1'
                        ],
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test 2'
                        ],
                    ]
                ],
                [
                    'invoice' => 1001,
                    'name' => 'Mr b',
                    'email' => 'admin@admin.com',
                    'age' => 35,
                    'status' => 'Inactive',
                    'status2' => 'Inactive',
                    "image" => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80',
                    'created_by' => [
                        'id' => 1,
                        'first_name' => 'Super',
                        'last_name' => 'Admin',
                        'email' => 'admin@admin.com',
                    ],
                    "users" => [
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test'
                        ],
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test 1'
                        ],
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test 2'
                        ],
                    ]
                ],
                [
                    'expandable_data' => 'Something to show',
                    'invoice' => 1002,
                    'name' => 'Mr c',
                    'email' => 'admin@admin.com',
                    'age' => 30,
                    'status' => 'Active',
                    'status2' => 'Active',
                    "image" => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80',
                    'created_by' => [
                        'id' => 1,
                        'first_name' => 'Super',
                        'last_name' => 'Admin',
                        'email' => 'admin@admin.com',
                    ],
                    "users" => [
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test'
                        ],
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test 1'
                        ],
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test 2'
                        ],
                    ]
                ],
                [
                    'invoice' => 1003,
                    'name' => 'Mr d',
                    'email' => 'admin@admin.com',
                    'age' => 35,
                    'status' => 'Inactive',
                    'status2' => 'Inactive',
                    "image" => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80',
                    'created_by' => [
                        'id' => 1,
                        'first_name' => 'Super',
                        'last_name' => 'Admin',
                        'email' => 'admin@admin.com',
                    ],
                    "users" => [
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test'
                        ],
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test 1'
                        ],
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test 2'
                        ],
                    ]
                ],
                [
                    'invoice' => 1004,
                    'name' => 'Mr f',
                    'email' => 'admin@admin.com',
                    'age' => 30,
                    'status' => 'Active',
                    'status2' => 'Active',
                    "image" => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80',
                    'created_by' => [
                        'id' => 1,
                        'first_name' => 'Super',
                        'last_name' => 'Admin',
                        'email' => 'admin@admin.com',
                    ],
                    "users" => [
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test'
                        ],
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test 1'
                        ],
                        [
                            "img" => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2134&q=80",
                            "alt" => 'Test 2'
                        ],
                    ]
                ],
            ],
            'current_page' => 1,
            'totalRow' => 50,
            'first_page_url' => 'http://emailmarketing.test/admin/app/brands?page=1',
            'from' => 1,
            'last_page' => 1,
            'last_page_url' => 'http://emailmarketing.test/admin/app/brands?page=1',
            'next_page_url' => null,
            'path' => 'http://emailmarketing.test/admin/app/brands',
            'per_page' => 15,
            'prev_page_url' => null,
            'to' => 2,
            'total' => 22,
        ];
    }

    public function getCardDataForTest(Request $request)
    {
        return [
            'req' => $request->all(),
            'data' => [
                [
                    "id" => 22,
                    "subject" => "Test template",
                    "default_content" => "<p class='text-center' style='font-size: 18px'>this is test template here.</p>",
                    "custom_content" => $this->getHTML('we_miss_you'),
                    "brand_id" => 1,
                    "thumbnail" => [
                        "path" => "/images/card-sample.jpg",
                    ],
                    "created_by" => [
                        "id" => 1,
                        "first_name" => "Super",
                        "last_name" => "Admin",
                        "email" => "admin@admin.com",
                        "last_login_at" => null,
                        "created_by" => null,
                        "status_id" => 1,
                        "invitation_token" => null,
                        "created_at" => "2020-04-26T04:18:26.000000Z",
                        "updated_at" => "2020-04-26T04:18:26.000000Z",
                        "deleted_at" => null,
                        "full_name" => "Super Admin"
                    ],
                    "updated_by" => null,
                    "created_at" => "2020-04-26T05:06:10.000000Z",
                    "updated_at" => "2020-04-26T05:06:10.000000Z",
                ],
                [
                    "id" => 23,
                    "subject" => "Test template 2",
                    "default_content" => "<p class='text-center' style='font-size: 18px'>this is test template here.</p>",
                    "custom_content" => $this->getHTML('its_your_birthday'),
                    "brand_id" => 1,
                    "thumbnail" => [
                        "path" => null,
                    ],
                    "created_by" => [
                        "id" => 1,
                        "first_name" => "Super",
                        "last_name" => "Admin",
                        "email" => "admin@admin.com",
                        "last_login_at" => null,
                        "created_by" => null,
                        "status_id" => 1,
                        "invitation_token" => null,
                        "created_at" => "2020-04-26T04:18:26.000000Z",
                        "updated_at" => "2020-04-26T04:18:26.000000Z",
                        "deleted_at" => null,
                        "full_name" => "Super Admin"
                    ],
                    "updated_by" => null,
                    "created_at" => "2020-04-26T05:06:10.000000Z",
                    "updated_at" => "2020-04-26T05:06:10.000000Z",
                ],
                [
                    "id" => 24,
                    "subject" => "Test template 3",
                    "default_content" => "<p class='text-center' style='font-size: 18px'>this is test template here.</p>",
                    "custom_content" => $this->getHTML('photography_advertisement'),
                    "brand_id" => 1,
                    "thumbnail" => null,
                    "created_by" => [
                        "id" => 1,
                        "first_name" => "Super",
                        "last_name" => "Admin",
                        "email" => "admin@admin.com",
                        "last_login_at" => null,
                        "created_by" => null,
                        "status_id" => 1,
                        "invitation_token" => null,
                        "created_at" => "2020-04-26T04:18:26.000000Z",
                        "updated_at" => "2020-04-26T04:18:26.000000Z",
                        "deleted_at" => null,
                        "full_name" => "Super Admin"
                    ],
                    "updated_by" => null,
                    "created_at" => "2020-04-26T05:06:10.000000Z",
                    "updated_at" => "2020-04-26T05:06:10.000000Z",
                ],
                [
                    "id" => 21,
                    "subject" => "Test template 4",
                    "default_content" => "<p class='text-center' style='font-size: 18px'>this is test template here.</p>",
                    "custom_content" => null,
                    "brand_id" => 1,
                    "thumbnail" => [
                        "path" => "/images/card-sample.jpg",
                    ],
                    "created_by" => [
                        "id" => 1,
                        "first_name" => "Super",
                        "last_name" => "Admin",
                        "email" => "admin@admin.com",
                        "last_login_at" => null,
                        "created_by" => null,
                        "status_id" => 1,
                        "invitation_token" => null,
                        "created_at" => "2020-04-26T04:18:26.000000Z",
                        "updated_at" => "2020-04-26T04:18:26.000000Z",
                        "deleted_at" => null,
                        "full_name" => "Super Admin"
                    ],
                    "updated_by" => null,
                    "created_at" => "2020-04-26T05:06:10.000000Z",
                    "updated_at" => "2020-04-26T05:06:10.000000Z",
                ],
            ],

            'current_page' => 1,
            'totalRow' => 40,
            'per_page' => 5,
            'total' => 40
        ];
    }

    public function test(Request $request)
    {
        // dd($request->all());


        $request->validate([
            // 'textValue' => 'required|min:3',
            // 'file' => 'required|image',
            // 'date' => 'required|date',
            // 'radio' => 'required|integer',
            // 'emailValue' => 'required|min:2',
            // 'numberValue' => 'required|min:2',
            // 'decimal' => 'required|min:1',
            // 'password' => 'required|min:3',
            // 'timeValue' => 'required|min:1',
            'custom_file' => 'required|image',
            // 'fardin' => 'required',
            'dropzoneValue.*' => 'file|mimes:jpeg,jpg,gif,png,pdf,zip|max:2048',
            // 'smart-select' => 'required|min:1',
            // 'selectValue' => 'required|min:1',
            // 'textarea' => 'required|min:5',
            // 'currency' => 'required',
            // 'checkbox' => 'required',
            // 'multi-select' => 'required',
            // 'textEditor' => 'required|min:5',
            // 'radio-buttons' => 'required'
        ]);

        // foreach($request->get('fardin') as $key => $value) {
        //     $rules['fardin.'.$key] = 'required|image';
        // }
        // $files = count($request->input('fardin')) - 1;
        // foreach(range(0, $files) as $index) {
        //     $rules['fardin.' . $index] = 'required|mimes:png,jpeg,jpg,gif|max:2048';
        // }
        // $request->validate($rules);

        dd($request->all());
    }

    public function testValue()
    {
        return [
            'textValue' => 'hi ',
            'file' => null,
            'date' => Carbon::now()->format('Y-m-d'),
            'radio' => 1,
            'emailValue' => 'test@test.com',
            'numberValue' => 20,
            'decimal' => 10,
            'password' => 'secret',
            'timeValue' => '12:10',
            'customFile' => '/images/core.png',
            'dropzone' => null,
            'smart-select' => 1,
            'selectValue' => 1,
            'textarea' => 'this is a text',
            'currency' => 200,
            'checkbox' => [
                'option 1', 'option 2'
            ],
            'multiSelect' => [1, 2],
            'textEditor' => '<h1>Hi I am rich text</h1>',
            'radio-buttons' => 1,
        ];
    }

    public function getCalendarEventForTest(Request $request)
    {
        return [
            [
                "id" => 21,
                "title" => "Zula Lehner scheduled a Meeting with Raynor PLC at 05:26",
                "activity_type_id" => 1,
                "contextable_type" => "App\\Models\\CRM\\Person\\Person",
                "contextable_id" => 46,
                "created_by" => [
                    "id" => 2,
                    "first_name" => "General",
                    "last_name" => "Admin",
                    "full_name" => "General Admin"
                ],
                "status_id" => 1,
                "start" => Carbon::now(),
                "end" => Carbon::now()->addHour(2),
                "start_time" => null,
                "end_time" => null,
                "created_at" => "2020-06-14T11:37:33.000000Z",
                "updated_at" => "2020-06-14T11:37:33.000000Z",
                "contextable" => [
                    "id" => 46,
                    "name" => "Salvador Steuber",
                    "address" => "980 Kristina Turnpike Apt. 057\nTristinberg, NC 18099-8901",
                    "contact_type_id" => 1,
                    "created_by" => 2,
                    "owner_id" => 1,
                    "created_at" => "2020-06-14T11:36:30.000000Z",
                    "updated_at" => "2020-06-14T11:36:30.000000Z",
                    "open_deals" => 0,
                    "closed_deals" => 0,
                    "total_deals" => 0,
                    "total_organizations" => 0,
                    "total_followers" => 0
                ],
                "activity_type" => [
                    "id" => 2,
                    "name" => "Meeting"
                ],
                "tags" => [
                ]
            ],
            [
                "id" => 22,
                "title" => "Zula Lehner scheduled a Meeting with Raynor PLC at 05:26",
                "activity_type_id" => 1,
                "contextable_type" => "App\\Models\\CRM\\Person\\Person",
                "contextable_id" => 46,
                "created_by" => [
                    "id" => 2,
                    "first_name" => "General",
                    "last_name" => "Admin",
                    "full_name" => "General Admin"
                ],
                "status_id" => 1,
                "started_at" => "1985-04-11",
                "ended_at" => null,
                "start_time" => null,
                "end_time" => null,
                "start" => "2020-09-09T13:05",
                "end" => "2020-09-09T15:55",
                "contextable" => [
                    "id" => 46,
                    "name" => "Salvador Steuber",
                    "address" => "980 Kristina Turnpike Apt. 057\nTristinberg, NC 18099-8901",
                    "contact_type_id" => 1,
                    "created_by" => 2,
                    "owner_id" => 1,
                    "created_at" => "2020-06-14T11:36:30.000000Z",
                    "updated_at" => "2020-06-14T11:36:30.000000Z",
                    "open_deals" => 0,
                    "closed_deals" => 0,
                    "total_deals" => 0,
                    "total_organizations" => 0,
                    "total_followers" => 0
                ],
                "activity_type" => [
                    "id" => 2,
                    "name" => "Meeting"
                ],
                "tags" => [
                ]
            ],
            [
                "id" => 24,
                "title" => "Zula Lehner scheduled a Meeting with Raynor PLC at 05:26",
                "activity_type_id" => 1,
                "contextable_type" => "App\\Models\\CRM\\Person\\Person",
                "contextable_id" => 46,
                "created_by" => [
                    "id" => 2,
                    "first_name" => "General",
                    "last_name" => "Admin",
                    "full_name" => "General Admin"
                ],
                "status_id" => 1,
                "start" => "2020-09-09T09:35",
                "end" => "2020-09-09T11:55",
                "start_time" => null,
                "end_time" => null,
                "created_at" => "2020-06-14T11:37:33.000000Z",
                "updated_at" => "2020-06-14T11:37:33.000000Z",
                "contextable" => [
                    "id" => 46,
                    "name" => "Salvador Steuber",
                    "address" => "980 Kristina Turnpike Apt. 057\nTristinberg, NC 18099-8901",
                    "contact_type_id" => 1,
                    "created_by" => 2,
                    "owner_id" => 1,
                    "created_at" => "2020-06-14T11:36:30.000000Z",
                    "updated_at" => "2020-06-14T11:36:30.000000Z",
                    "open_deals" => 0,
                    "closed_deals" => 0,
                    "total_deals" => 0,
                    "total_organizations" => 0,
                    "total_followers" => 0
                ],
                "activity_type" => [
                    "id" => 2,
                    "name" => "Meeting"
                ],
                "tags" => [
                ]
            ],
            [
                "id" => 25,
                "title" => "Zula Lehner scheduled a Meeting with Raynor PLC at 05:26",
                "activity_type_id" => 1,
                "contextable_type" => "App\\Models\\CRM\\Person\\Person",
                "contextable_id" => 46,
                "created_by" => [
                    "id" => 2,
                    "first_name" => "General",
                    "last_name" => "Admin",
                    "full_name" => "General Admin"
                ],
                "status_id" => 1,
                "start" => "2020-09-09T01:00",
                "end" => "2020-09-09T03:00",
                "start_time" => null,
                "end_time" => null,
                "created_at" => "2020-06-14T11:37:33.000000Z",
                "updated_at" => "2020-06-14T11:37:33.000000Z",
                "contextable" => [
                    "id" => 46,
                    "name" => "Salvador Steuber",
                    "address" => "980 Kristina Turnpike Apt. 057\nTristinberg, NC 18099-8901",
                    "contact_type_id" => 1,
                    "created_by" => 2,
                    "owner_id" => 1,
                    "created_at" => "2020-06-14T11:36:30.000000Z",
                    "updated_at" => "2020-06-14T11:36:30.000000Z",
                    "open_deals" => 0,
                    "closed_deals" => 0,
                    "total_deals" => 0,
                    "total_organizations" => 0,
                    "total_followers" => 0
                ],
                "activity_type" => [
                    "id" => 2,
                    "name" => "Meeting"
                ],
                "tags" => [
                ]
            ],
        ];
    }

    private function getHTML($name)
    {
        return file_get_contents(database_path("factories/templates/{$name}.html"));
    }

    public $multi_option = [
        [
            'id' => 1,
            'name' => 'Cricket'
        ],
        [
            'id' => 2,
            'name' => 'Football'
        ],
        [
            'id' => 3,
            'name' => 'Basketball'
        ],
        [
            'id' => 4,
            'name' => 'Baseball'
        ],
    ];

    public function addNewMultiCreateOption(Request $request)
    {
        array_push($this->multi_option, [
            'id' => 5,
            'name' => $request->name
        ]);

        return [
            'new_id' => 5,
            'list' => $this->multi_option
        ];
    }

    public $tag_option = [
        [
            'id' => 1,
            'name' => 'Red',
            'color' => '#72C2EE'
        ],
        [
            'id' => 2,
            'name' => 'Black',
            'color' => '#72f2ee'
        ],
        [
            'id' => 3,
            'name' => 'Yellow',
            'color' => '#72C268'
        ],
        [
            'id' => 4,
            'name' => 'Blue',
            'color' => '#F2C268'
        ],
    ];
    public $tags = [];
    public function getTags() {
        return $this->tags;
    }
    public function storeAndGetTagOptions(Request $request) {
        array_push($this->tag_option, [
            'id' => 5,
            'name' => $request->name,
            'color' => $request->color
        ]);
        return [
            'new_id' => 5,
            'list' => $this->tag_option
        ];
    }

}
