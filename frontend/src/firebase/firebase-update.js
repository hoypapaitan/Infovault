import { db } from './index.js'
import { doc, updateDoc } from 'firebase/firestore'
import { Loading, Notify } from 'quasar'

const updateDocument = async (collectionName, docId, data) => {
  return new Promise((resolve, reject) => {
    try {
      const docRef = doc(db, collectionName, docId)
      updateDoc(docRef, data).then(() => {
        resolve()
      })
    } catch (err) {
      Notify.create({
        type: 'negative',
        message: err.message
      })
      reject(err.message)
    }
  })
}

export default updateDocument
