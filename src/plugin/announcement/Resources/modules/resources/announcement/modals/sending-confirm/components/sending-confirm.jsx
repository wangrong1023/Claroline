import React from 'react'
import {PropTypes as T} from 'prop-types'
import omit from 'lodash/omit'

import {Modal} from '#/main/app/overlays/modal/components/modal'
import {ListData} from '#/main/app/content/list/containers/data'

import {trans} from '#/main/app/intl/translation'
import {UserList} from '#/main/core/administration/community/user/components/user-list'

import {selectors} from '#/plugin/announcement/resources/announcement/store/selectors'

const SendingConfirmModal = props =>
  <Modal
    {...omit(props, 'filters', 'aggregateId', 'announcementId', 'handleConfirm')}
    className="data-picker-modal"
    title={trans('send')}
    icon="fa fa-fw fa-user"
    bsSize="lg"
  >
    <ListData
      name={selectors.STORE_NAME+'.selected.list'}
      fetch={{
        url: ['claro_announcement_validate', {aggregateId: props.aggregateId, id: props.announcementId}],
        autoload: true
      }}
      definition={UserList.definition}
      card={UserList.card}
      selectable={false}
      filterable={false}
      paginated={true}
      sortable={true}
    />
    <button
      className="modal-btn btn btn-primary"
      onClick={() => {
        props.fadeModal()
        props.handleConfirm()
      }}
    >
      {trans('send', {}, 'actions')}
    </button>
  </Modal>

SendingConfirmModal.propTypes = {
  aggregateId: T.string,
  announcementId: T.string,
  handleConfirm: T.func.isRequired,
  fadeModal: T.func.isRequired
}

export {
  SendingConfirmModal
}
