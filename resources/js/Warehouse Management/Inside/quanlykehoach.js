$('.btn-secondary').click(function() {
    var supplyId = $(this).data('id'); // Lấy ID từ data-id của nút
    $('#supplyId').val(supplyId); // Đặt ID vào trường ẩn
});
