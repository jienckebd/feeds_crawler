<?php

namespace Drupal\feeds_crawler\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for contact messages and contact forms.
 */
class FeedsCrawlerTypeRouteProvider extends DefaultHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $route_collection = parent::getRoutes($entity_type);

    $entity_type_id = $entity_type->id();
    $label_singular = $entity_type->getSingularLabel();
    $label_plural = $entity_type->getPluralLabel();
    $admin_permission = $entity_type->getAdminPermission();

    if (!is_string($label_plural)) {
      $label_plural = $label_plural->__toString();
    }

    if (!is_string($label_singular)) {
      $label_singular = $label_singular->__toString();
    }

    if ($entity_type->hasLinkTemplate('add-form')) {
      $route = (new Route($entity_type->getLinkTemplate('add-form')))
        ->addDefaults([
          '_entity_form' => "{$entity_type_id}.add",
          '_title' => "Add {$label_singular}",
        ])
        ->setOption('_entity_type_id', $entity_type_id)
        ->addRequirements([
          '_permission' => $admin_permission
        ]);
      $route_collection->add("entity.{$entity_type_id}.add_form", $route);
    }

    if ($entity_type->hasLinkTemplate('collection')) {
      $route = (new Route($entity_type->getLinkTemplate('collection')))
        ->addDefaults([
          '_entity_list' => $entity_type_id,
          '_title' => $label_plural,
        ])
        ->setOption('_entity_type_id', $entity_type_id)
        ->addRequirements([
          '_permission' => $admin_permission,
        ]);
      $route_collection->add("entity.{$entity_type_id}.collection", $route);
    }

    if ($entity_type->hasLinkTemplate('edit-form')) {
      $route = (new Route($entity_type->getLinkTemplate('edit-form')))
        ->addDefaults([
          '_entity_form' => "{$entity_type_id}.edit",
          '_title' => $label_singular,
        ])
        ->setOption('_entity_type_id', $entity_type_id)
        ->addRequirements([
          '_permission' => $admin_permission
        ]);
      $route_collection->add("entity.{$entity_type_id}.edit_form", $route);
    }

    if ($entity_type->hasLinkTemplate('delete-form')) {
      $route = (new Route($entity_type->getLinkTemplate('delete-form')))
        ->addDefaults([
          '_entity_form' => "{$entity_type_id}.delete",
          '_title' => $label_singular,
        ])
        ->setOption('_entity_type_id', $entity_type_id)
        ->addRequirements([
          '_permission' =>$admin_permission
        ]);
      $route_collection->add("entity.{$entity_type_id}.delete_form", $route);
    }

    return $route_collection;
  }

}
