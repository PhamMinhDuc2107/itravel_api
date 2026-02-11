<?php

return [
    // Search & Query
    'search' => [
        'invalid_column' => 'The search column ":column" is invalid. Allowed columns: :allowed.',
    ],

    // Resource not found
    'not_found' => [
        'default' => 'The requested resource was not found.',
        'with_id' => 'The requested :resource with ID [:id] was not found.',
        'with_field' => 'The requested :resource with :field ":value" was not found.',
    ],

    // CRUD Operations
    'crud' => [
        'created' => ':resource has been created successfully.',
        'updated' => ':resource has been updated successfully.',
        'deleted' => ':resource has been deleted successfully.',
        'restored' => ':resource has been restored successfully.',
    ],

    // Generic
    'success' => 'Operation completed successfully.',
    'error' => 'An error occurred. Please try again.',
    'unauthorized' => 'You are not authorized to perform this action.',
    'forbidden' => 'Access forbidden.',
    'validation_failed' => 'The given data was invalid.',

    // File Upload
    'upload' => [
        'success' => 'File uploaded successfully.',
        'failed' => 'File upload failed.',
        'invalid_type' => 'Invalid file type. Allowed types: :types.',
        'too_large' => 'File size exceeds the maximum limit of :max.',
    ],

    // Export
    'export' => [
        'success' => 'Data exported successfully.',
        'failed' => 'Export failed. Please try again.',
        'no_data' => 'No data available for export.',
    ],
];
