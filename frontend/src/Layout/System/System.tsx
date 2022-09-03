import React, { ReactNode } from 'react'
import styles from './System.module.css'

function System({ children }: { children: ReactNode }): JSX.Element {
  return (
    <div className={styles.layout}>
      <div className={styles.content}>{children}</div>
    </div>
  )
}

export default System
