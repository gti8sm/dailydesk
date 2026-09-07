<?php $__env->startSection('title', 'Invitation - ' . $appName); ?>

<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #3b82f6;"><?php echo e($appName); ?></h2>
    
    <p>Bonjour,</p>
    
    <p>Vous êtes invité(e) à rejoindre la plateforme <strong><?php echo e($appName); ?></strong> pour gérer la famille <strong><?php echo e($family->family_name); ?></strong>.</p>
    
    <p>Vous pourrez consulter les présences de vos enfants, signaler des absences, et mettre à jour les informations (allergies, régime alimentaire, inscriptions garderie/cantine).</p>
    
    <p style="margin: 30px 0;">
        <a href="<?php echo e($url); ?>" 
           style="background: #3b82f6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">
            Créer mon compte
        </a>
    </p>
    
    <p style="color: #666; font-size: 12px;">
        Ce lien expirera après une certaine durée. Si vous n'avez pas demandé cette invitation, ignorez cet email.
    </p>
</div>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/simon/Documents/Web/Communeo/resources/views/emails/family-invitation.blade.php ENDPATH**/ ?>