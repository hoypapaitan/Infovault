import { db } from './index.js'
import { doc, setDoc, collection } from 'firebase/firestore'
import { Loading, Notify } from 'quasar'

const createDocument = async (collectionName, data) => {
  return new Promise((resolve, reject) => {
    // Loading.show()

    try {
      const docRef = doc(collection(db, collectionName))
      setDoc(docRef, data).then(() => {
        // Loading.hide()
        resolve()
      })
    } catch (err) {
      // Loading.hide()
      Notify.create({
        type: 'negative',
        message: err.message
      })
      reject(err.message)
    }
  })
}

export default createDocument