import React from 'react'

type Props = {
  label: string
  htmlFor: string | null
}

function InputLabel({ label, htmlFor = null, ...rest }: Props): JSX.Element {
  return (
    <label className="input-label" htmlFor={htmlFor || undefined} {...rest}>
      {label}
    </label>
  )
}

export default InputLabel
