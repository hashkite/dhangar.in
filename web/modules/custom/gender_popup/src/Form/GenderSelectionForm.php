<?php

namespace Drupal\gender_popup\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Provides a form for selecting gender.
 */
class GenderSelectionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gender_selection_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['gender'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select your gender'),
      '#options' => [
        'male' => $this->t('Male'),
        'female' => $this->t('Female'),
      ],
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => '::ajaxSubmit',
        'wrapper' => 'gender-selection-wrapper',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the current user.
    $user = User::load(\Drupal::currentUser()->id());

    // Save the selected gender.
    $user->set('field_gender', $form_state->getValue('gender'));
    $user->save();

    // Display a confirmation message.
    \Drupal::messenger()->addMessage($this->t('Your gender has been saved.'));
  }

  /**
   * AJAX callback for form submission.
   */
  public function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    // Get the current user.
    $user = User::load(\Drupal::currentUser()->id());

    // Save the selected gender.
    $user->set('field_gender', $form_state->getValue('gender'));
    $user->save();

    // Return a response indicating success.
    $response = new \Drupal\Core\Ajax\AjaxResponse();
    $response->addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#gender-selection-wrapper', 'Gender saved!'));

    // Close the dialog after submission.
    $response->addCommand(new \Drupal\Core\Ajax\CloseDialogCommand());
    return $response;
  }

}
