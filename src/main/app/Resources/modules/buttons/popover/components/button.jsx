import React from 'react'
import identity from 'lodash/identity'
import omit from 'lodash/omit'

import {PropTypes as T, implementPropTypes} from '#/main/app/prop-types'
import {PopoverOverlay} from '#/main/app/overlays/popover/components/overlay'

import {Button as ButtonTypes} from '#/main/app/buttons/prop-types'
import {CallbackButton} from '#/main/app/buttons/callback/components/button'

const PopoverButton = (props) =>
  <PopoverOverlay
    {...props.popover}
    id={`${props.id}-popover`}
  >
    <CallbackButton
      {...omit(props, 'popover')}
      callback={identity}
    >
      {props.children}
    </CallbackButton>
  </PopoverOverlay>

implementPropTypes(PopoverButton, ButtonTypes, {
  id: T.string.isRequired,
  popover: T.shape({
    className: T.string,
    label: T.node,
    position: T.oneOf(['top', 'bottom', 'left', 'right']),
    content: T.node.isRequired
  }).isRequired
})

export {
  PopoverButton
}
