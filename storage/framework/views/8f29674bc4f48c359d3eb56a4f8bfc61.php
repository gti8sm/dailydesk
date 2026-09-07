<?php $__env->startSection('title', 'Connexion'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
            <h2 class="text-4xl font-extrabold text-gray-900">
                <?php
                    try {
                        echo \App\Models\Setting::get('app_name', config('app.name', 'DailyDesk'));
                    } catch (\Exception $e) {
                        echo config('app.name', 'DailyDesk');
                    }
                ?>
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Plateforme de gestion municipale
            </p>
        </div>
        
        <div class="bg-white shadow-xl rounded-2xl p-8">
            <?php if(session('error')): ?>
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <p class="text-sm text-red-700"><?php echo e(session('error')); ?></p>
            </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <p class="text-sm text-green-700"><?php echo e(session('success')); ?></p>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>
                
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-gray-400 mr-2"></i>
                        Email ou Identifiant
                    </label>
                    <input id="email" name="email" type="text" required autofocus
                           class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all"
                           placeholder="votre@email.com ou votre identifiant"
                           value="<?php echo e(old('email')); ?>">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-5">
                    <label for="credential" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key text-gray-400 mr-2"></i>
                        Mot de passe ou Code PIN
                    </label>
                    <input id="credential" name="credential" type="password" required
                           class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all"
                           placeholder="Entrez votre mot de passe ou code PIN">
                    <?php $__errorArgs = ['credential'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p class="mt-2 text-xs text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Vous pouvez utiliser votre mot de passe ou votre code PIN (4-6 chiffres)
                    </p>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            <i class="fas fa-clock mr-1 text-gray-400"></i>
                            Rester connecté 30 jours
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="<?php echo e(route('password.request')); ?>" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                            Mot de passe oublié ?
                        </a>
                    </div>
                </div>

                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Se connecter
                </button>
            </form>
        </div>

        <div class="text-center">
            <p class="text-xs text-gray-500">
                <i class="fas fa-shield-alt mr-1"></i>
                Connexion sécurisée
            </p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/simon/Documents/Web/Communeo/resources/views/auth/login.blade.php ENDPATH**/ ?>