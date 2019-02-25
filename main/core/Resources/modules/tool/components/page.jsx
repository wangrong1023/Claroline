import React from 'react'
import {PropTypes as T} from 'prop-types'
import omit from 'lodash/omit'

import {trans} from '#/main/app/intl/translation'
import {PageFull} from '#/main/app/page/components/full'

/*import {ToolIcon} from '#/main/core/tool/components/icon'*/
import {getToolPath, showToolBreadcrumb} from '#/main/core/tool/utils'

// TODO : display tool icon

const ToolPage = props =>
  <PageFull
    title={trans(props.name, {}, 'tools')}
    showBreadcrumb={showToolBreadcrumb(props.currentContext.type, props.currentContext.data)}
    path={[].concat(getToolPath(props.name, props.currentContext.type, props.currentContext.data), props.path)}

    {...omit(props, 'name', 'currentContext', 'path')}
  >
    {props.children}
  </PageFull>

ToolPage.propTypes = {
  // tool props
  name: T.string.isRequired,
  currentContext: T.shape({
    type: T.oneOf(['administration', 'desktop', 'workspace']),
    data: T.object
  }).isRequired,

  // page props
  subtitle: T.string,
  toolbar: T.string,
  actions: T.any,
  path: T.arrayOf(T.object), // TODO : correct typing
  children: T.any
}

ToolPage.defaultProps = {
  path: []
}

export {
  ToolPage
}