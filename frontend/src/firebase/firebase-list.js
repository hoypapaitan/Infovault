import { db } from './index.js'
import { getDocs, collection } from 'firebase/firestore'
import { Loading, Notify } from 'quasar'

const listDocuments = async (collectionName) => {
  return new Promise((resolve, reject) => {
    // Loading.show()

    try {
      getDocs(collection(db, collectionName)).then((querySnapshot) => {
        const documents = []
        querySnapshot.forEach((document) => {
          documents.push({ id: document.id, ...document.data() })
        })
        Loading.hide()
        resolve(documents)
      })
    } catch (err) {
      Loading.hide()
      Notify.create({
        type: 'negative',
        message: err.message
      })
      reject(err.message)
    }
  })
}

export default listDocuments