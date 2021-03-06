import React from 'react'
import {PropTypes as T} from 'prop-types'
import omit from 'lodash/omit'

import {url} from '#/main/app/api'
import {trans} from '#/main/app/intl/translation'
import {Modal} from '#/main/app/overlays/modal/components/modal'
import {DetailsData} from '#/main/app/content/details/components/data'

import {route} from '#/main/core/resource/routing'
import {ResourceType} from '#/main/core/resource/components/type'
import {ResourceNode as ResourceNodeTypes} from '#/main/core/resource/prop-types'

const AboutModal = props =>
  <Modal
    {...omit(props, 'resourceNode')}
    icon="fa fa-fw fa-info"
    title={trans('about')}
    subtitle={props.resourceNode.name}
    poster={props.resourceNode.poster ? props.resourceNode.poster.url : undefined}
  >
    <DetailsData
      meta={true}
      data={props.resourceNode}
      sections={[
        {
          title: trans('general'),
          primary: true,
          fields: [
            {
              name: 'meta.type',
              label: trans('type'),
              type: 'string',
              hideLabel: true,
              render: (resourceNode) => {
                const NodeType =
                  <ResourceType
                    name={resourceNode.meta.type}
                    mimeType={resourceNode.meta.mimeType}
                  />

                return NodeType
              }
            }, {
              name: 'url',
              type: 'url',
              label: trans('url', {}, 'data'),
              calculated: (resourceNode) => `${url(['claro_index', {}, true])}#${route(resourceNode)}`
            }, {
              name: 'meta.description',
              label: trans('description'),
              type: 'string'
            }, {
              name: 'parent',
              label: trans('parent'),
              type: 'resource'
            }, {
              name: 'workspace',
              label: trans('workspace'),
              type: 'workspace'
            }, {
              name: 'id',
              label: trans('id'),
              type: 'string',
              calculated: (resourceNode) => resourceNode.id + ' / ' + resourceNode.autoId
            }
          ]
        }
      ]}
    />
  </Modal>

AboutModal.propTypes = {
  resourceNode: T.shape(
    ResourceNodeTypes.propTypes
  ).isRequired
}

export {
  AboutModal
}
