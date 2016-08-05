<?php

return [
    'general' => [
        'name' => 'ChipIn'
    ],

    'user' => [
        'menu' => 'Users',

        'list-header' => 'User List',
        'search-header' => 'User Search',
        'create-header' => 'Add new user',
        'edit-header' => 'Edit user',

        'password-header' => 'Change user password',
        'general-header' => 'General information',
        'extended-header' => 'Extended information',
        'image-header' => 'User image',

        'name' => 'Name',
        'first-name' => 'First Name',
        'last-name' => 'Last Name',
        'email' => 'Email',
        'password' => 'Password',
        'password-check' => 'Retype password',
        'birthday' => 'Birthday',
        'country' => 'Country of residence',
        'nationality' => 'Nationality',
        'address' => 'Address',
        'occupation' => 'Occupation',
        'income' => 'Income range',
        'created' => 'Registered at',
        'image' => 'Image',
        'active' => 'Active',

        'goals-search' => 'Filter user goals',
        'last-login' => 'Last login at',
    ],

    'category' => [
        'menu' => 'Categories',

        'list-header' => 'Category List',
        'search-header' => 'Category Search',
        'create-header' => 'Add new category',
        'edit-header' => 'Edit category',

        'general-header' => 'General information',
        'image-header' => 'Category image',

        'name' => 'Name',
        'image' => 'Image',
        'count' => 'Count of goals created',
    ],

    'goal' => [
        'menu' => 'Goals',
        'sub-menu' => 'User goals',

        'view-header' => ':Type goal',
        'general-header' => 'General',
        'payment-header' => 'Payments',
        'time-header' => 'Dates',
        'image-header' => 'Image',
        'participants-header' => 'Participants',
        'invites-header' => 'Invites',

        'search-placeholder' => 'Enter name..',

        'name' => 'Name',
        'category_id' => 'Category',
        'category' => 'Category',
        'type' => 'Type',
        'start_date' => 'Start Date',
        'due_date' => 'Due Date',
        'amount' => 'Amount',
        'timer' => 'Timer Type',
        'image' => 'Image',
        'user-name' => 'Owner',
        'user' => 'User',
        'progress' => 'Progress',
        'last-payment' => 'Last Payment',
        'created' => 'Created at',
        'start-date' => 'Start date',
        'due-date' => 'Due date',
        'status' => 'Status',

        'list-header' => 'Goal List',
        'search-header' => 'Goal Search',

        'type-competition' => 'competition',
        'type-personal' => 'personal',
        'type-collective' => 'collective',

        'no-payments' => 'none',

        'find-category' => 'Choose category..',
        'find-type' => 'Choose type..',

        'filtered' => '(filtered)',

        'status-active' => 'Active',
        'status-cashback' => 'Cashback',
        'status-unknown' => 'Status unknown',

        'status-label' => ' (Status: :Status)',

        'invite-status' => 'Status',
        'invite-sent' => 'Sent at',
        'invite-changed' => 'Status changed at',

        'no-invites' => 'There are no invites for this goal',
        'no-participants' => 'There are no participants in this goal',
    ],

    'payment' => [
        'menu' => 'Payments',
    ],

    'image' => [
        'current' => ':Item image',
        'upload-replace' => 'Upload new image (will replace existing)',
        'upload-new' => 'Upload image',
        'delete' => 'Delete image',
        'name' => 'Image'
    ],

    'btn' => [
        'add' => 'Add new :item',
        'back-to-list' => 'Back to :item list',

        'save' => 'Save changes',

        'block' => 'Block',
        'activate' => 'Activate',
        'delete' => 'Delete',
    ],

    'msg' => [
        'empty-list' => 'There is no :item registered.',
        'empty-search' => 'Can\'t find any :item according to your search string. Try refining your search.',
        'password-change' => 'To change password type new one here',
        'action-confirm' => 'Are you sure you want to :action selected :items?',
    ],

    'err' => [
        'pwd-len' => 'Passwords must be at least six characters',
        'pwd-check' => 'Please enter the same password as above',
    ],
];