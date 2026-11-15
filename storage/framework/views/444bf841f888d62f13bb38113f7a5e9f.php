<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e(ucwords(str_replace('-', ' ', $type))); ?> report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; }
        h1 { color: #0c4a6e; font-size: 18px; }
        p { color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #082f49; color: white; text-align: left; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; }
        tr:nth-child(even) { background: #f8fafc; }
    </style>
</head>
<body>
    <h1><?php echo e(ucwords(str_replace('-', ' ', $type))); ?> report</h1>
    <p>Generated <?php echo e(now()->format('M d, Y H:i')); ?></p>
    <table>
        <thead><tr><?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><th><?php echo e($header); ?></th><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr><?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td><?php echo e($value ?? '—'); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="<?php echo e(count($headers)); ?>">No records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/exports/report.blade.php ENDPATH**/ ?>