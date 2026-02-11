<?php

return [
    // Tìm kiếm & Truy vấn
    'search' => [
        'invalid_column' => 'Cột tìm kiếm ":column" không hợp lệ. Các cột cho phép: :allowed.',
    ],

    // Không tìm thấy tài nguyên
    'not_found' => [
        'default' => 'Không tìm thấy tài nguyên được yêu cầu.',
        'with_id' => 'Không tìm thấy :resource với ID [:id].',
        'with_field' => 'Không tìm thấy :resource với :field ":value".',
    ],

    // Thao tác CRUD
    'crud' => [
        'created' => ':resource đã được tạo thành công.',
        'updated' => ':resource đã được cập nhật thành công.',
        'deleted' => ':resource đã được xóa thành công.',
        'restored' => ':resource đã được khôi phục thành công.',
    ],

    // Chung
    'success' => 'Thao tác thành công.',
    'error' => 'Đã xảy ra lỗi. Vui lòng thử lại.',
    'unauthorized' => 'Bạn không có quyền thực hiện hành động này.',
    'forbidden' => 'Truy cập bị từ chối.',
    'validation_failed' => 'Dữ liệu không hợp lệ.',

    // Tải file
    'upload' => [
        'success' => 'Tải file thành công.',
        'failed' => 'Tải file thất bại.',
        'invalid_type' => 'Định dạng file không hợp lệ. Các định dạng cho phép: :types.',
        'too_large' => 'Kích thước file vượt quá giới hạn :max.',
    ],

    // Xuất dữ liệu
    'export' => [
        'success' => 'Xuất dữ liệu thành công.',
        'failed' => 'Xuất dữ liệu thất bại. Vui lòng thử lại.',
        'no_data' => 'Không có dữ liệu để xuất.',
    ],
];
