<?php

namespace Drupal\gender_popup\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides a form for editing the user profile.
 */
class UserProfileEditForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_profile_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, User $user = NULL) {
    // Load the current user if no user entity is passed.
    if ($user === NULL) {
      $user = User::load(\Drupal::currentUser()->id());
    }
  
    // Get the username using the field method.
    $form['ProfileImage'] = [
        '#type' => 'file',
        '#title' => $this->t('Profile Image'),
        '#description' => $this->t('Upload a profile image.'),
        '#default_value' => $user->get('user_picture')->entity ? $user->get('user_picture')->entity->id() : NULL,
    ];    

    $form['FirstName'] = [
        '#type' => 'textfield',
        '#title' => $this->t('FirstName'),
        '#default_value' => $user->get('field_first_name')->value,  // Assuming 'field_about' is a custom field.
      ];

      $form['lastName'] = [
        '#type' => 'textfield',
        '#title' => $this->t('lastName'),
        '#default_value' => $user->get('field_last_name')->value,  // Assuming 'field_about' is a custom field.
      ];

    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#default_value' => $user->get('name')->value,  // Correct way to get the username.
      '#required' => TRUE,
    ];

    // Get the email using the field method.
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email address'),
      '#default_value' => $user->get('mail')->value,  // Correct way to get the email.
      '#required' => TRUE,
    ];
  
    // Example custom field for About.
    $form['About'] = [
      '#type' => 'textarea',
      '#title' => $this->t('About'),
      '#default_value' => $user->get('field_about')->value,  // Assuming 'field_about' is a custom field.
    ];
    
  
    // Gender field as an example.
    $form['gender'] = [
      '#type' => 'radios',
      '#title' => $this->t('Gender'),
      '#options' => [
        'male' => $this->t('Male'),
        'female' => $this->t('Female'),
      ],
      '#default_value' => $user->get('field_gender')->value, // Assuming 'field_gender' is a custom field.
    ];
  
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save changes'),
    ];
  
    return $form;
  }
  

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('username'))) {
      $form_state->setErrorByName('username', $this->t('The username field is required.'));
    }

    if (empty($form_state->getValue('email'))) {
      $form_state->setErrorByName('email', $this->t('The email address field is required.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Load the current user.
    $user = User::load(\Drupal::currentUser()->id());
  
    // Get the new username from the form.
    $new_username = $form_state->getValue('username');
  
    // Check if a user with the new username already exists.
    $existing_user = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->loadByProperties(['name' => $new_username]);
  
    // If a user with this username exists and it's not the current user, show an error.
    if (!empty($existing_user) && reset($existing_user)->id() != $user->id()) {
      \Drupal::messenger()->addError($this->t('The username "@name" is already taken. Please choose another one.', ['@name' => $new_username]));
      return;
    }
  
    // Set the username and email.
    $user->set('name', $new_username);  // Correct way to set the username.
    $user->set('mail', $form_state->getValue('email'));  // Correct way to set the email.
  
    // Update custom fields like bio and gender.
    $user->set('field_about', $form_state->getValue('About'));  // Assuming 'field_bio' is a custom field.
    $user->set('field_gender', $form_state->getValue('gender'));
    $user->set('field_first_name', $form_state->getValue('FirstName'));
    $user->set('field_last_name', $form_state->getValue('lastName'));
    $user->set('user_picture', $form_state->getValue('ProfileImage'));
    
    // Save the user entity.
    try {
      $user->save();
      \Drupal::messenger()->addMessage($this->t('Your profile has been updated.'));
    } catch (\Exception $e) {
      \Drupal::messenger()->addError($this->t('An error occurred while saving the profile: @message', ['@message' => $e->getMessage()]));
    }
  
    // Redirect to the user's profile page after saving.
    $form_state->setRedirect('entity.user.canonical', ['user' => $user->id()]);
  }
  
  

}
