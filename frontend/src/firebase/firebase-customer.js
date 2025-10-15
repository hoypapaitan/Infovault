import { db } from './index.js'
import { collection, query, where, limit, getDocs, startAt } from 'firebase/firestore'
import { Loading, Notify } from 'quasar'

const customerList = async (collectionPath, filterField, filterValue, pageLimit = 10, pageNumber = 0) => {
  return new Promise((resolve, reject) => {
    // Loading.show()

    try {
      let q

      if (filterValue) {
        q = query(
          collection(db, collectionPath),
          where(filterField, '==', filterValue),
          startAt(pageNumber),
          limit(pageLimit)
        )
      } else {
        q = query(
            collection(db, collectionPath),
            startAt(pageNumber),
            limit(pageLimit)
        )
      }

      getDocs(q).then((querySnapshot) => {
        const documents = {
            list: [],
            totalData: 0
        }
        querySnapshot.forEach((doc) => {
          documents.list.push({ id: doc.id, ...doc.data() })
        })

        documents.totalData = documents.list.length

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

export default customerList
