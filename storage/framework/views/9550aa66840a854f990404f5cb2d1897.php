<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit <?php echo e($person->full_name); ?> - BioVerify AI</title>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-4xl mx-auto px-6 py-8">

        
        <div class="flex items-center justify-between mb-6">

            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Edit Person
                </h1>

                <p class="text-gray-500 mt-1">
                    Update person information
                </p>
            </div>

            <a href="<?php echo e(route('people.show', $person)); ?>"
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Back
            </a>

        </div>


        
        <?php if($errors->any()): ?>

            <div class="mb-6 bg-red-100 border border-red-300
                        text-red-800 rounded-lg px-4 py-3">

                <p class="font-semibold mb-2">
                    Please correct the following:
                </p>

                <ul class="list-disc list-inside text-sm">

                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <li><?php echo e($error); ?></li>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>

            </div>

        <?php endif; ?>


        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">

            <div class="px-6 py-4 border-b border-gray-200">

                <h2 class="text-xl font-semibold text-gray-800">
                    Personal Information
                </h2>

            </div>


            <form method="POST"
                  action="<?php echo e(route('people.update', $person)); ?>"
                  class="p-6">

                <?php echo csrf_field(); ?>

                <?php echo method_field('PUT'); ?>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Person ID
                        </label>

                        <input
                            type="text"
                            name="person_id"
                            value="<?php echo e(old('person_id', $person->person_id)); ?>"
                            required
                            maxlength="50"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        <?php $__errorArgs = ['person_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-sm mt-1">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>

                        <select
                            name="status"
                            required
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                            <option value="Active"
                                <?php echo e(old('status', $person->status) === 'Active' ? 'selected' : ''); ?>>
                                Active
                            </option>

                            <option value="Inactive"
                                <?php echo e(old('status', $person->status) === 'Inactive' ? 'selected' : ''); ?>>
                                Inactive
                            </option>

                        </select>

                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-sm mt-1">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            First Name
                        </label>

                        <input
                            type="text"
                            name="first_name"
                            value="<?php echo e(old('first_name', $person->first_name)); ?>"
                            required
                            maxlength="100"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-sm mt-1">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Middle Name
                        </label>

                        <input
                            type="text"
                            name="middle_name"
                            value="<?php echo e(old('middle_name', $person->middle_name)); ?>"
                            maxlength="100"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        <?php $__errorArgs = ['middle_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-sm mt-1">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            value="<?php echo e(old('last_name', $person->last_name)); ?>"
                            required
                            maxlength="100"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-sm mt-1">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Suffix
                        </label>

                        <input
                            type="text"
                            name="suffix"
                            value="<?php echo e(old('suffix', $person->suffix)); ?>"
                            maxlength="20"
                            placeholder="Jr., Sr., III"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        <?php $__errorArgs = ['suffix'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-sm mt-1">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="<?php echo e(old('email', $person->email)); ?>"
                            maxlength="255"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-sm mt-1">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Mobile Number
                        </label>

                        <input
                            type="text"
                            name="mobile_number"
                            value="<?php echo e(old('mobile_number', $person->mobile_number)); ?>"
                            maxlength="30"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        <?php $__errorArgs = ['mobile_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-sm mt-1">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>

                </div>


                
                <div class="flex justify-end gap-3 mt-8 pt-6
                            border-t border-gray-200">

                    <a href="<?php echo e(route('people.show', $person)); ?>"
                       class="px-5 py-2.5 bg-gray-200 text-gray-700
                              rounded-lg hover:bg-gray-300">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white
                               rounded-lg hover:bg-blue-700">

                        Save Changes

                    </button>

                </div>

            </form>

        </div>

    </div>

</body>
</html><?php /**PATH C:\xampp\htdocs\bioverify-ai\resources\views/people/edit.blade.php ENDPATH**/ ?>