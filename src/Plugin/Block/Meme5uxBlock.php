<?php

namespace Drupal\meme_5ux\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Meme 5ux' block.
 *
 * @Block(
 *   id = "meme_5ux_block",
 *   admin_label = @Translation("Meme 5ux Block"),
 *   category = @Translation("Custom"),
 * )
 */
class Meme5uxBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'meme_text' => '',
    // Set default value for subdomain select.
      'subdomain' => 'mrt',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    // Subdomain select options.
    $subdomain_options = [
      'mrt' => $this->t('I am pity the fool who ...'),
      'prof' => $this->t('Good news everyone ...'),
      'jasper' => $this->t("... that's a paddlin'"),
    ];

    $form['subdomain'] = [
      '#type' => 'select',
      '#title' => $this->t('Subdomain'),
      '#description' => $this->t('Select a meme option.'),
      '#options' => $subdomain_options,
      '#default_value' => $config['subdomain'],
    ];

    $form['meme_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Meme Text'),
      '#description' => $this->t('Enter the short text to be displayed on the Meme.'),
      '#default_value' => $config['meme_text'],
      "#required" => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $meme_text = $form_state->getValue('meme_text');
    $this->setConfigurationValue('meme_text', $meme_text);
    $subdomain = $form_state->getValue('subdomain');
    $this->setConfigurationValue('subdomain', $subdomain);
    // Transform the meme_text value as needed.
    $meme_string = str_replace(["\n", "\r", "\t"], ' ', $meme_text);
    $meme_string = preg_replace('/\s+/', ' ', $meme_string);
    $meme_string = str_replace(" ", '-', $meme_string);
    $meme_string = strtolower($meme_string);
    $meme_string = urlencode($meme_string);
    $meme_url = "https://" . $subdomain . ".5ux.com/" . $meme_string . ".png";
    // Save the transformed meme_text value and subdomain
    // to the block configuration.
    $this->setConfigurationValue('meme_url', $meme_url);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $meme_url = $config['meme_url'];
    $meme_text = $config['meme_text'];

    return [
      '#theme' => 'meme_5ux_block_template',
      '#meme_url' => $meme_url,
      '#meme_text' => $meme_text,
      '#attached' => [
        'library' => ['meme_5ux/meme_5ux_block'],
      ],
    ];
  }

}
