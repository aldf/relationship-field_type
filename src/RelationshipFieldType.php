<?php namespace Anomaly\RelationshipFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\Contract\RelationFieldTypeInterface;
use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Entry\EntryModel;
use Anomaly\Streams\Platform\Model\EloquentModel;

/**
 * Class RelationshipFieldType
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\RelationshipFieldType
 */
class RelationshipFieldType extends FieldType implements RelationFieldTypeInterface
{

    /**
     * The input view.
     *
     * @var string
     */
    protected $inputView = 'anomaly.field_type.relationship::input';

    /**
     * Get the relation.
     *
     * @param EntryModel $model
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|mixed|null
     */
    public function getRelation(EntryModel $model)
    {
        return $model->hasOne(array_get($this->config, 'related'), 'id', $this->getColumnName());
    }

    /**
     * Get the options.
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];

        foreach ($this->getModelOptions() as $option) {

            $option['selected'] = ($option['value'] == $this->getValue());

            $options[] = $option;
        }

        return $options;
    }

    /**
     * Get options from the model.
     *
     * @return array
     */
    protected function getModelOptions()
    {
        $model = $this->getRelatedModel();

        if (!$model instanceof EloquentModel) {
            return [];
        }

        $options = [];

        foreach ($model->all() as $entry) {

            $value = $entry->getKey();

            if ($title = array_get($this->config, 'title')) {
                $title = $entry->{$title};
            }

            if (!$title) {
                $title = $entry->getTitle();
            }

            $entry = $entry->toArray();

            $options[] = compact('value', 'title', 'entry');
        }

        return $options;
    }

    /**
     * Get the related model.
     *
     * @return null
     */
    protected function getRelatedModel()
    {
        $model = array_get($this->config, 'related');

        if (!$model) {
            return null;
        }

        return app()->make($model);
    }

    /**
     * Get the database column name.
     *
     * @return null|string
     */
    public function getColumnName()
    {
        return parent::getColumnName() . '_id';
    }
}
